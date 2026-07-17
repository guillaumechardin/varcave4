@props([
    'subject',
    'messageDfltBody',
])
<div id="contact-form">
    <div class="field">
        <label class="label">{{ __('varcave.contact_form.enter_name') }}</label>
        <div class="control">
            <input  id="contact-name"
                    name="contact-name"
                    autocomplete="contact-name"
                    class="input" 
                    type="text" 
                    placeholder="{{ __('varcave.contact_form.hint_enter_name') }}"
                    required
            />
        </div>
    </div>

    <div class="field">
        <label class="label">{{ __('varcave.contact_form.email') }}</label>
    <div class="control has-icons-left has-icons-right">
        <input  id="contact-mail-from"
                name="email"
                autocomplete="email"
                class="input"
                type="email"
                placeholder="{{ __('varcave.contact_form.hint_email') }}" 
                value=""
                required
        />
        <span class="icon is-small is-left">
            <i class="bi bi-envelope"></i>
        </span>
        <span class="icon is-small is-right not-valid is-hidden">
            <i class="bi bi-exclamation-triangle"></i>
        </span>
    </div>
        <p class="help is-danger not-valid is-hidden">{{ __('varcave.contact_form.invalid_email') }}</p>
    </div>

    <div class="field">
        <label class="label">{{ __('varcave.contact_form.subject') }}</label>
        <div class="control">
            <input id="contact-msg-subject" name="contact-msg-subject" class="input" placeholder="{{ __('varcave.contact_form.hint_subject') }}" value="{{ $subject }}"/>
        </div>
    </div>

    <div class="field">
        <label class="label">{{ __('varcave.contact_form.message_body') }}</label>
        <div class="control">
            <textarea id="contact-msg-body" class="textarea" placeholder="{{ __('varcave.contact_form.hint_message_body') }}"></textarea>
        </div>
    </div>

    <div class="field">
        <div class="control">
            <label class="checkbox">
                <input id="contact-send-copy-to-user" type="checkbox" checked>
                {{ __('varcave.contact_form.send_copy') }}
            </label>
        </div>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button id="send-contact-form" class="button is-link">{{ Str::ucfirst(__('varcave.general.send')) }}</button>
        </div>
    </div>
</div>