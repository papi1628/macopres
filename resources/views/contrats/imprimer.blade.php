<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrat — {{ $programme->ecole->nom }}</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }
        .barre-outils { flex-wrap: wrap; gap: 8px; }
        .page { max-width: 800px; width: 100%; margin: 0 auto; padding: 30px 20px 60px; font-size: 13px; line-height: 1.6; box-sizing: border-box; }
        .article-titre { font-weight: 800; text-transform: uppercase; margin-top: 26px; margin-bottom: 8px; color: #0C447C; }
        ul.engagement { margin: 8px 0 0 0; padding-left: 18px; }
        ul.engagement li { margin-bottom: 3px; }
        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 16px; }

        @media (max-width: 640px) {
            .page { padding: 20px 16px 40px; font-size: 12.5px; }
            .signature-grid { grid-template-columns: 1fr; gap: 28px; }
            h1 { font-size: 15px !important; }
        }

        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
            .page { padding: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">Contrat de prestation — {{ $programme->ecole->nom }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    @php
        $contrat  = $programme->contrat;
        $ecole    = $programme->ecole;
        $echeances = $programme->echeancesPaiement->sortBy('numero_versement')->values();

        $delaiTexte = $contrat->delai_livraison_texte
            ?: ($contrat->date_limite_livraison ? 'avant le ' . $contrat->date_limite_livraison->format('d/m/Y') : '……………………………');

        $engagementLignes = $contrat->description_engagement
            ? explode("\n", $contrat->description_engagement)
            : [];
    @endphp

    <div class="page" style="text-align:center;">

        <h1 style="text-align:center; font-size:18px; font-weight:800; letter-spacing:.02em; margin-bottom:24px;">
            CONTRAT DE PRESTATION DE SERVICES {{ $programme->annee_scolaire }}
        </h1>

        <p>Entre les soussignés</p>

        <p style="margin-top:14px;"><strong>D'une part</strong></p>
        <p>
            MACOPRES, entreprise de confection, de communication et de prestation de services représentée par
            Monsieur Masse BA, Président Directeur Général.<br>
            Contact : 77 659 42 18<br>
            Agissant en qualité de prestataire.
        </p>

        <p style="margin-top:14px;"><strong>D'autre part</strong></p>
        <p>
            {{ mb_strtoupper($ecole->nom) }}
            Représenté par {{ $contrat->representant_client ?: '……………………………' }}
            {{ $contrat->representant_client_role ?: '' }}
            Numéro de téléphone {{ $ecole->contact_telephone ?: '……………………………' }}
        </p>

        <p style="margin-top:14px;">Il a été convenu et arrêté ce qui suit :</p>

        {{-- ARTICLE 1 --}}
        <p class="article-titre">Article 1 : Engagement</p>
        <p>
            MACOPRES s'engage à assurer les fournitures et la réalisation des produits jusqu'à leur livraison
            pour {{ mb_strtoupper($ecole->nom) }} sur la base d'un bon de commande qui détermine les quantités suivantes :
        </p>
        @if(count($engagementLignes) > 0)
            <ul class="engagement" style="text-align:center;">
                @foreach($engagementLignes as $ligne)
                    <li>{{ ltrim($ligne, '- ') }}</li>
                @endforeach
            </ul>
        @endif

        {{-- ARTICLE 2 --}}
        <p class="article-titre">Article 2 : Facturation</p>
        <p>
            Le montant total du bon de commande est de {{ number_format($contrat->montant_total, 0, ',', ' ') }} FCFA
            ({{ \App\Support\NombreEnLettres::enMontant($contrat->montant_total) }}).
        </p>

        {{-- ARTICLE 3 --}}
        <p class="article-titre">Article 3 : Délai de livraison</p>
        <p>
            Macopres a l'obligation de livrer la totalité de la commande {{ $delaiTexte }}
            sous peine de résiliation du contrat sauf un arrangement à l'amiable.
        </p>

        {{-- ARTICLE 4 --}}
        <p class="article-titre">Article 4 : Modalité de paiement</p>
        <p>
            Les conditions de paiement qui seront fixées sont invariables et les montants fixés
            et leurs dates d'échéances sont inchangeables.
        </p>
        @if($echeances->count() > 0)
            <ul class="engagement" style="text-align:left;">
                @foreach($echeances as $i => $ech)
                    <li>
                        le {{ \App\Support\NombreEnLettres::ordinal($ech->numero_versement) }} versement est prévu
                        le {{ $ech->date_prevue->format('d/m/Y') }} pour un montant de
                        {{ number_format($ech->montant_prevu, 0, ',', ' ') }} FCFA
                        ({{ \App\Support\NombreEnLettres::enMontant($ech->montant_prevu) }})
                        @if($i === $echeances->count() - 1)
                            représentant le solde de la facture.
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p style="color:#94a3b8; font-style:italic;">Échéancier non encore renseigné.</p>
        @endif

        {{-- ARTICLE 5 --}}
        <p class="article-titre">Article 5 : Encaissement</p>
        <p>Les encaissements se feront :</p>
        <ul class="engagement" style="text-align:center;">
            <li>Soit par chèque au nom de MACOPRES</li>
            <li>
                Soit par virement bancaire<br>
                BDK N° SN 191 01 102 050600713201 57<br>
                Macopres Group<br>
                Adresse cité FADIA N°14 DAKAR / Sénégal
            </li>
            <li>Soit par transfert sur Wave ou Orange Money au 77 659 42 18</li>
            <li>Soit le client se déplace à Macopres pour effectuer un versement en espèce</li>
            <li>
                Soit enfin par un agent mandaté par Macopres qui devra présenter une note de service pour chaque
                encaissement ; pour ce cas, l'agent a l'obligation, au-delà de la note de service, de remettre un
                reçu en bonne et due forme avec l'entête et le cachet de Macopres Group.
            </li>
        </ul>
        <p style="margin-top:10px;">
            Les décharges sont formellement interdites et ne seront en aucun cas reconnues par Macopres.<br>
            <strong>NB :</strong> tout versement remis à une autre personne que l'agent mandaté avec tous les
            documents cités ne sera pas reconnu en cas de litige.
        </p>

        {{-- ARTICLE 6 --}}
        <p class="article-titre">Article 6 : Signature</p>

        <div class="signature-grid">
            <div>
                <p style="font-weight:700;">Prestataire</p>
                <p>
                    Je soussigné Monsieur Masse BA, Président Directeur Général de Macopres, certifie avoir lu
                    et approuvé les termes de production et de livraison, et m'engage à les respecter scrupuleusement.
                </p>
                <p style="margin-top:40px;">Signature cachet</p>
                <p style="font-weight:700; margin-top:30px;">M. MASSE BA<br>PDG Macopres</p>
            </div>
            <div>
                <p style="font-weight:700;">Client</p>
                <p>
                    Je soussigné {{ $contrat->representant_client ?: '……………………………' }}
                    {{ $contrat->representant_client_role ?: '' }} de l'école {{ mb_strtoupper($ecole->nom) }},
                    certifie avoir lu et approuvé les termes de versement, et m'engage à les respecter scrupuleusement.
                </p>
                <p style="margin-top:40px;">Signature cachet</p>
                <p style="font-weight:700; margin-top:30px;">
                    {{ $contrat->representant_client ?: '……………………………' }}<br>
                    {{ $contrat->representant_client_role ?: '' }}
                </p>
            </div>
        </div>

        <p style="margin-top:40px;">
            Fait à DAKAR le {{ $contrat->date_signature?->format('d/m/Y') ?? '……………………………' }}
        </p>

        <p style="
        font-size:9px;
        color:#cbd5e1;
        margin-top:40px;
        text-align:center;
        ">

        Contrat généré le {{ $contrat->date_signature?->format('d/m/Y à H:i') }} — MACOPRES

        </p>
    </div>
</body>
</html>