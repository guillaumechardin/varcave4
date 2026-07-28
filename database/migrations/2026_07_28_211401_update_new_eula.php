<?php

use App\Models\Eula;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $html = <<<'EOF'
<h2>Charte d'utilisation du site fichiertopo.fr</h2><p>Veuillez lire attentivement ces informations qui concernent l’usage et l’accès à ce site de publication.</p><h2>Droit d’accès</h2><p>Un accès à l'inventaire des cavités du Var (www.fichiertopo.fr) vous a été accordé par le CDS83.</p><p>Cet accès (associé à votre compte utilisateur et votre mot de passe) vous a été octroyé de manière personnelle. Vos codes d’accès ne doivent en aucun cas être communiqués à un tiers ou utilisés par une autre personne.</p><p>Le non-respect de ces règles entraînera la révocation de vos accès.</p><h2>Usage des données</h2><p>En utilisant ce site vous acceptez d’utiliser ces informations uniquement&nbsp; dans le cadre d’une activité spéléologique de loisir.</p><p>Pour toute autre utilisation de ces données dans un autre but, par exemple commercial, dans le cadre d’un travail salarié ou bénévole ou encore dans le cadre d’une étude scientifique, devra se faire après demande écrite auprès du CDS83 et avec son accord.</p><p>Le CDS83 peut être contacté&nbsp;par courrier électronique :&nbsp;</p><p style="text-align: left;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;contact@speleo83cds.fr</p><p>ou à son adresse postale :</p><p>&nbsp; &nbsp; &nbsp;Maison des Sports<br>&nbsp; &nbsp; &nbsp;133 avenue du Général Brosset<br>&nbsp; &nbsp; &nbsp;83200 TOULON</p><h2>Publication des données</h2><p>En tant qu'usager de ce site, nous vous demandons de ne pas divulguer les données que vous y trouverez.<br>Toute diffusion d'une quelconque manière que ce soit devra se faire avec un accord écrit du CDS83.<br>Le non-respect de ces règles entraînera la révocation de vos accès.</p><h2>Transmission d’informations et droit d’auteur</h2><p>A compter du 01/06/2025, date de la mise en ligne de la première version de cette&nbsp; charte, si vous décidez de publier des informations pour la mise à jour ou l’enrichissement de cet inventaire, vous acceptez automatiquement d’en céder les droits au CDS83.</p><p>Cela concerne sans exhaustivité les éléments suivants :&nbsp; nouvelle cavité, mises à jour, topographies, descriptions, fiche d’équipement, etc.</p><p>Si vous refuser cette transmission de droits, veuillez nous contacter par email ou via courrier postal aux adresses indiquée au paragraphe <strong>"Usage des données"</strong></p><h2>Données personnelles</h2><p>Les données personnelles collectées par ce site sont destinées aux besoins de gestion du site. En aucun cas ces données ne seront communiquées à des tiers.</p><p>Vous avez la possibilité de modifier les données vous concernant dans la rubrique “mon compte”.</p><h2>Collecte d’informations</h2><p>En acceptant cette charte d’utilisation, vous autorisez également le CDS83 à collecter des données liées à votre activité sur ce site pour des besoins divers (statistiques, suivi des connexions, sécurité, etc.).</p><br><br><br><br>
EOF;
        DB::table('eulas')
            ->where('lang', 'fr')
            ->update(['content' => $html]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
