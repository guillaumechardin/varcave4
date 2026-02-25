@include('varcave.template.header')
@include('varcave.template.navbar')

<script src="/varcave/profile.js"></script>
<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ __('varcave.profile.confirm-password2') }}</p>
            <p class="subtitle">Veuillez confirmer votre mot de passe pour accéder aux fonctions sécurisées</p>
        </div>
        
    </section>
    <x-varcave.confirm-password/>
    
</section>

@include('varcave.template.footer')