<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiCredential;
use App\Models\SeoProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoProjectController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $projects = SeoProject::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    // ── Create Form ───────────────────────────────────────────────────────────

    public function create(): View
    {
        $credentials = ApiCredential::where('user_id', auth()->id())
            ->where('provider', 'shopware')
            ->where('is_active', true)
            ->get();

        return view('projects.create', compact('credentials'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $v = $request->validate([
            'name'                    => 'required|string|max:100',
            'shopware_url'            => 'required|url|max:255',
            'shopware_credential_id'  => 'nullable|exists:api_credentials,id',
        ]);

        SeoProject::create([
            'user_id'                => auth()->id(),
            'name'                   => $v['name'],
            'shopware_url'           => rtrim($v['shopware_url'], '/'),
            'shopware_credential_id' => $v['shopware_credential_id'] ?? null,
            'is_active'              => true,
        ]);

        return redirect()->route('projects.index')
            ->with('success', "Projekt „{$v['name']}\" wurde erstellt.");
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(SeoProject $project): View
    {
        $this->authorizeProject($project);

        $credentials = ApiCredential::where('user_id', auth()->id())
            ->where('provider', 'shopware')
            ->where('is_active', true)
            ->get();

        return view('projects.edit', compact('project', 'credentials'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, SeoProject $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $v = $request->validate([
            'name'                   => 'required|string|max:100',
            'shopware_url'           => 'required|url|max:255',
            'shopware_credential_id' => 'nullable|exists:api_credentials,id',
            'is_active'              => 'boolean',
        ]);

        $project->update([
            'name'                   => $v['name'],
            'shopware_url'           => rtrim($v['shopware_url'], '/'),
            'shopware_credential_id' => $v['shopware_credential_id'] ?? null,
            'is_active'              => $v['is_active'] ?? $project->is_active,
        ]);

        return redirect()->route('projects.index')
            ->with('success', "Projekt „{$v['name']}\" wurde aktualisiert.");
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(SeoProject $project): RedirectResponse
    {
        $this->authorizeProject($project);
        $name = $project->name;
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', "Projekt „{$name}\" wurde gelöscht.");
    }

    // ── Authorization ─────────────────────────────────────────────────────────

    private function authorizeProject(SeoProject $project): void
    {
        abort_unless($project->user_id === auth()->id(), 403);
    }
}
