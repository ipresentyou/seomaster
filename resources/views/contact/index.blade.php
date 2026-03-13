@extends('layouts.public')

@section('content')

<style>
.contact-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.contact-header {
    text-align: center;
    margin-bottom: 40px;
}

.contact-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--text-1);
}

.contact-header p {
    font-size: 16px;
    color: var(--text-2);
    line-height: 1.6;
}

.contact-form {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: normal;
    color: #202124;
    margin-bottom: 8px;
}

.form-label .required {
    color: #d93025;
    margin-left: 2px;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 12px -0px 12px 10px;
    margin: 0;
    border: 2px solid #dadce0;
    border-radius: 8px;
    font-size: 14px;
    color: #202124;
    background: #ffffff;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input::placeholder,
.form-textarea::placeholder {
    color: #80868b;
    opacity: 1;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26,115,232,0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
}

.form-error {
    color: #d93025;
    font-size: 12px;
    margin-top: 4px;
    font-weight: 500;
}

.submit-btn {
    background: #1a73e8;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    box-shadow: 0 2px 8px rgba(26,115,232,0.3);
}

.submit-btn:hover {
    background: #1557b0;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(26,115,232,0.4);
}

.submit-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(26,115,232,0.3);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.contact-info {
    margin-top: 40px;
    padding: 24px;
    background: rgba(26,115,232,0.05);
    border: 1px solid rgba(26,115,232,0.2);
    border-radius: 12px;
    text-align: center;
}

.contact-info h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 12px;
    color: var(--text-1);
}

.contact-info p {
    color: var(--text-2);
    line-height: 1.6;
}

.contact-info a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
}

.contact-info a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .contact-form {
        padding: 24px;
    }
    
    .contact-header h1 {
        font-size: 28px;
    }
}
</style>

<div class="contact-container">
    <div class="contact-header">
        <h1>📧 Kontakt</h1>
        <p>Hast du Fragen oder benötigst Unterstützung? Wir sind hier, um dir zu helfen!</p>
    </div>

    <div class="contact-form">
        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        Name <span class="required">*</span>
                    </label>
                    <input type="text" name="name" class="form-input" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        E-Mail <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-input" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Betreff <span class="required">*</span>
                </label>
                <input type="text" name="subject" class="form-input" 
                       value="{{ old('subject') }}" required
                       placeholder="z.B. Frage zu meinem Abonnement, technisches Problem, ...">
                @error('subject')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Nachricht <span class="required">*</span>
                </label>
                <textarea name="message" class="form-textarea" required
                          placeholder="Beschreibe dein Anliegen so detailliert wie möglich...">{{ old('message') }}</textarea>
                @error('message')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                📤 Nachricht senden
            </button>
        </form>
    </div>

    <div class="contact-info">
        <h3>📞 Weitere Kontaktmöglichkeiten</h3>
        <p>
            Du erreichst uns auch unter:<br>
            <strong>E-Mail:</strong> <a href="mailto:support@seomaster.de">support@seomaster.de</a><br>
            <strong>Antwortzeit:</strong> Normalerweise innerhalb von 24 Stunden<br>
            <strong>Support-Zeiten:</strong> Mo-Fr, 9:00-18:00 Uhr
        </p>
    </div>
</div>

@endsection
