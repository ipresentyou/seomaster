<?php

namespace App\Http\Controllers\Seo;

use App\Models\SeoProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AltTextController extends BaseSeoController
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request, SeoProject $project)
    {
        $this->bootProject($project);

        $selectedSc   = $request->input('sc', '');
        $selectedLang = $request->input('lang', '');
        $limit        = (int) $request->input('max', 50);
        $filterType   = $request->input('filter', 'missing');

        $meta = $this->buildPageMeta($selectedSc, $selectedLang);
        $salesChannels = $meta['salesChannels'];

        if (! $selectedSc)   $selectedSc   = array_key_first($salesChannels) ?? '';
        if (! $selectedLang) $selectedLang = array_key_first($meta['domains'][$selectedSc] ?? []) ?? '';

        $rows = [];
        $totalSize    = 0;
        $missingCount = 0;

        if ($selectedSc && $selectedLang) {
            $mediaData = $this->shopware->getMedia($selectedLang, $limit, $filterType === 'missing');
            $included  = $mediaData['included'] ?? [];

            // Build product context lookup
            $includedProducts = [];
            foreach ($included as $inc) {
                if ($inc['type'] === 'product') {
                    $includedProducts[$inc['id']] = $inc;
                }
            }

            foreach (($mediaData['data'] ?? []) as $media) {
                $a = $media;

                $productCtx = [];
                foreach (($media['relationships']['productMedia']['data'] ?? []) as $pmData) {
                    foreach ($included as $inc) {
                        if ($inc['type'] === 'product_media' && $inc['id'] === $pmData['id']) {
                            $prodId = $inc['relationships']['product']['data']['id'] ?? null;
                            if ($prodId && isset($includedProducts[$prodId])) {
                                $productCtx[] = $includedProducts[$prodId]['name'];
                            }
                        }
                    }
                }

                $row = [
                    'id'             => $media['id'],
                    'fileName'       => $a['fileName']  ?? 'unknown.jpg',
                    'url'            => $a['url']        ?? '',
                    'alt'            => $a['alt']        ?? '',
                    'title'          => $a['title']      ?? '',
                    'fileSize'       => $a['fileSize']   ?? 0,
                    'mimeType'       => $a['mimeType']   ?? 'image/jpeg',
                    'productContext' => implode(', ', array_unique($productCtx)),
                ];

                $rows[]     = $row;
                $totalSize  += $row['fileSize'];
                if (empty($row['alt'])) $missingCount++;
            }
        }

        return view('seo.alttext.index', array_merge($meta, compact(
            'project', 'rows', 'selectedSc', 'selectedLang',
            'limit', 'filterType', 'totalSize', 'missingCount', 'salesChannels'
        )));
    }

    // ── AI Generate Alt-Text ──────────────────────────────────────────────────

    public function generate(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'imageUrl'           => 'required|url',
            'fileName'           => 'required|string',
            'productContext'     => 'nullable|string',
            'customInstructions' => 'nullable|string|max:3000',
            'targetLang'         => 'nullable|string',
            'domain'             => 'nullable|string',
        ]);

        try {
            $result = $this->ai->generateAltText(
                imageUrl:           $v['imageUrl'],
                fileName:           $v['fileName'],
                targetLanguage:     $v['targetLang']         ?? '',
                productContext:     $v['productContext']      ?? '',
                domainName:         $v['domain']             ?? '',
                customInstructions: $v['customInstructions'] ?? '',
            );
        } catch (\RuntimeException $e) {
            return $this->err($e->getMessage());
        }

        if (isset($result['error'])) return $this->err($result['error']);

        $this->log('alt_text.generated', 'media', '', [], 500);

        return $this->ok($result);
    }

    // ── Save Alt-Text ─────────────────────────────────────────────────────────

    public function save(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'mediaId' => 'required|string',
            'langId'  => 'required|string',
            'alt'     => 'required|string|max:200',
        ]);

        $ok = $this->shopware->saveMediaAlt($v['mediaId'], $v['langId'], $v['alt']);

        if ($ok) {
            $this->log('alt_text.saved', 'media', $v['mediaId'], ['alt' => $v['alt']]);
        }

        return $ok ? $this->ok() : $this->err('Shopware PATCH fehlgeschlagen', 500);
    }

    // ── Batch Save ────────────────────────────────────────────────────────────

    public function batchSave(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'items'           => 'required|array|max:100',
            'items.*.mediaId' => 'required|string',
            'items.*.langId'  => 'required|string',
            'items.*.alt'     => 'required|string|max:200',
        ]);

        $saved = $failed = 0;

        foreach ($v['items'] as $item) {
            $ok = $this->shopware->saveMediaAlt($item['mediaId'], $item['langId'], $item['alt']);
            $ok ? $saved++ : $failed++;
            if ($ok) {
                $this->log('alt_text.saved', 'media', $item['mediaId'], ['alt' => $item['alt']]);
            }
        }

        return $this->ok(['saved' => $saved, 'failed' => $failed]);
    }
}
