/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

function check_residents() {
    $(document).ready(function() {
        var countAdult = parseFloat($('select#countAdultVar').val());
        var countChild = parseFloat($('select#countChildVar').val());

        for (var i=1;i<9;i++) {
            if ( i <= countAdult ) {
                $('#adult_'+i).css('display','block');
            } else {
                $('#adult_'+i).css('display','none');
            }
        }
        for (var i=1;i<9;i++) {
            if ( i <= countChild ) {
                $('#child_'+i).css('display','block');
            } else {
                $('#child_'+i).css('display','none');
            }
        }

        if ( countChild > 0 ) {
            for (var i=1;i<=countChild;i++) {
                var actAdultFirst = $('select#adult_id_first_' + i);
                var actAdultSecond = $('select#adult_id_second_' + i);
                var selectedAdultIdFirst = parseFloat(actAdultFirst.val());
                var selectedAdultIdSecond = parseFloat(actAdultSecond.val());
                actAdultFirst.children().remove();
                actAdultSecond.children().remove();
                actAdultSecond.append('<option value="">bitte w&aumlhlen</option>');
                for (var j=1;j<=countAdult;j++ ) {
                    if ( j == selectedAdultIdFirst )
                        actAdultFirst.append('<option value="'+j+'" selected="selected">Erwachsener '+j+'</option>');
                    else
                        actAdultFirst.append('<option value="'+j+'">Erwachsener '+j+'</option>');
                    if ( j == selectedAdultIdSecond )
                        actAdultSecond.append('<option value="'+j+'" selected="selected">Erwachsener '+j+'</option>');
                    else
                        actAdultSecond.append('<option value="'+j+'">Erwachsener '+j+'</option>');
                }
            }
        }

    });
}

// jquery ui init
$(function() {
/* accordion */
    $( ".accordion" ).accordion({
        header: "> div > h3",
        heightStyle: "content",
        collapsible: true
    });

/* tab */
    $( ".householdtabs" ).tabs();

/* datepicker*/
    $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "1900:2013",
        dateFormat: "yy-mm-dd"
    });

/* tooltip */
    $(document).tooltip({
        content: function () {
            return $(this).prop('title');
        }
    });


/* autocomplete*/
  var availableTags = [
    "Landwirte/Landwirtinnen, Bauern/Bäuerinnen",
    "Landwirtschaftliche Gehilfen/Gehilfinnen",
    "Sonstige landwirtschaftliche Berufe, wna",
    "Obstbauern/-bäuerinnen",
    "Rebbauern/-bäuerinnen",
    "Gemüsebauern/-bäuerinnen und Gemüsegärtner/innen",
    "Grossvieh- und Grosstierzüchter/innen und -pfleger/innen",
    "Kleinvieh- und Kleintierzüchter/innen und -pfleger/innen",
    "Geflügelzüchter/innen und -pfleger/innen",
    "Fischzüchter/innen und -pfleger/innen",
    "Übrige Berufe der Tierbetreuung",
    "Gärtner/innen und verwandte Berufe",
    "Floristen/Floristinnen",
    "Förster/innen",
    "Forstwarte/Forstwartinnen und Waldarbeiter/innen",
    "Jagdberufe und Wildhüter/innen",
    "Berufe der Fischerei",
    "Käser/innen und Molkeristen/Molkeristinnen",
    "Metzger/innen und andere Fleischverarbeiter/innen",
    "Bäcker/innen, Konditoren/Konditorinnen, Confiseure/Confiseurinnen",
    "Müller/innen",
    "Übrige Berufe der Lebensmittelverarbeitung",
    "Brauer/innen, Mälzer/innen",
    "Weinküfer/innen, Wein- und Getränketechnologen/-technologinnen uvB",
    "Tabakwarenhersteller/innen und -verarbeiter/innen",
    "Lebens- und Genussmitteltester/innen und Degustierer/innen",
    "Garnhersteller/innen",
    "Stoffhersteller/innen",
    "Textilveredler/innen, Färber/innen",
    "Übrige Berufe der Textilherstellung",
    "Schneider/innen",
    "Näher/innen",
    "Sticker/innen",
    "Übrige Berufe der Textilverarbeitung",
    "Gerber/innen, Lederhersteller/innen",
    "Schuhmacher/innen, andere Schuhhersteller/innen",
    "Sattler/innen",
    "Sonstige Lederhersteller/innen und -verarbeiter/innen uvB",
    "Fellverarbeiter/innen, Kürschner/innen",
    "Glasbläser/innen, Apparateglasbläser/innen",
    "Feinwerk- und Instrumentenoptiker/innen",
    "Glasschleifer/innen, -polierer/innen sowie übrige -verarbeiter/innen",
    "Berufe der Keramikherstellung und -behandlung",
    "Giesser/innen uvB",
    "Kernmacher/innen, Gussformer/innen",
    "Sonstige Berufe der Metallerzeugung",
    "Berufe der spanlosen Metallverformung",
    "Berufe der metallischen Oberflächenveredelung",
    "Metallschleifer/innen sowie -polierer/innen",
    "Werkzeugmaschinisten/-maschinistinnen",
    "Fräser/innen und Hobler/innen",
    "Metallbohrer/innen",
    "Dreher/innen",
    "Decolleteure/Decolleteurinnen und Schraubenmacher/innen",
    "Sonstige Metallbearbeiter/innen und -verformer/innen",
    "Schweisser/innen und andere Berufe der Metallverbindung",
    "Anlagen- und Apparatebauer/innen",
    "Spengler/innen (Industrie)",
    "Schmiede/Schmiedinnen, wna",
    "Metallbauer/innen und Metallbauschlosser/innen",
    "Konstruktionsschlosser/innen",
    "Schlosser/innen, wna",
    "Sonstige Metallverarbeiter/innen",
    "Mechaniker/innen",
    "Feinmechaniker/innen und Mikromechaniker/innen",
    "Maschinenschlosser/innen und Maschinenmonteure/monteurinnen uvB",
    "Mechaniker/innen für Einrichtung und Unterhalt und sonstige Mechaniker/innen",
    "Sonstige Monteure/Monteurinnen",
    "Auto- und andere Fahrzeugelektriker/innen und -elektroniker/innen",
    "Elektromechaniker/innen",
    "Unterhaltungselektroniker/innen",
    "Netzelektriker/innen, Kabelmonteure/-monteurinnen",
    "Elektrowickler/innen uvB",
    "Andere Elektrikerberufe, wna",
    "Elektronikerberufe",
    "Telefon- und Telegraphenhandwerker/innen",
    "Uhrenmacher/innen",
    "Sonstige Berufe der Uhrenindustrie",
    "Berufe des Fahrzeugbaus (Land, Wasser, Luft)",
    "Automechaniker/innen",
    "Motorrad- und Fahrradmechaniker/innen",
    "Landmaschinen-, Baumaschinen- und Kleinapparatemechaniker/innen",
    "Lackierer/innen (Fahrzeug, Industrie)",
    "Tankwarte/-wartinnen, Autoserviceleute, wna",
    "Säger/innen, Holzzuschneider/innen",
    "Drechsler/innen",
    "Möbelschreiner/innen",
    "Bauschreiner/innen",
    "Sonstige Schreiner/innen",
    "Übrige Berufe der Holzverarbeitung sowie Berufe der Kork-, Korb- und Flechtwarenherstellung",
    "Holzbeizer/innen, -polierer/innen",
    "Einrahmer/innen, Vergolder/innen und andere Berufe der Holzveredelung",
    "Papiertechnologen/-technologinnen uvB",
    "Übrige Berufe der Herstellung und Verarbeitung von Papier",
    "Typographen/Typographinnen uvB",
    "Lithographen/Lithographinnen",
    "Reprografen/-grafinnen",
    "Layouter/innen und übrige Berufe der Druckvorbereitung",
    "Drucker/innen",
    "Vervielfältiger/innen und Photokopierer/innen",
    "Buchbinder/innen",
    "Sonstige Buchbinderei- und Ausrüstberufe",
    "Laboranten/Laborantinnen, Laboristen/Laboristinnen, wna",
    "Chemikanten/Chemikantinnen, Cheministen/Cheministinnen",
    "Fotolaboranten/Fotolaborantinnen",
    "Übrige Berufe der Chemieverfahren",
    "Kunststoffhersteller/innen und -verarbeiter/innen",
    "Gummiverarbeiter/innen",
    "Warennachseher/innen und -sortierer/innen",
    "Verpacker/innen",
    "Magaziner/innen, Lageristen/Lageristinnen",
    "Sonstige be- und verarbeitende Berufe",
    "Architekten/Architektinnen",
    "Bauingenieure/-ingenieurinnen",
    "Informatikingenieure/-ingenieurinnen",
    "Maschineningenieure/-ingenieurinnen",
    "Heizungs-, Lüftungs- und Klimaanlageningenieure/-ingenieurinnen",
    "Elektroingenieure/-ingenieurinnen",
    "Elektronik- und Mikrotechnikingenieure/-ingenieurinnen",
    "Forstingenieure/-ingenieurinnen",
    "Agronomen/Agronominnen",
    "Kultur- und Vermessungsingenieure/-ingenieurinnen, Geometer/innen",
    "Orts-, Siedlungs- und Landschaftsplaner/innen",
    "Chemieingenieure/-ingenieurinnen und Lebensmittelingenieure/-ingenieurinnen",
    "Übrige Ingenieure/Ingenieurinnen",
    "Elektrotechniker/innen",
    "Elektroniktechniker/innen",
    "Hoch- und Tiefbautechniker/innen, Bauführer/innen",
    "Maschinentechniker/innen",
    "Textiltechniker/innen",
    "Fernmeldetechniker/innen",
    "Heizungs-, Lüftungs- und Klimatechniker/innen",
    "Fahr- und Flugzeugtechniker",
    "Übrige Techniker/innen",
    "Hoch- und Tiefbauzeichner/innen",
    "Vermessungszeichner/innen",
    "Technische Zeichner/innen",
    "Metallbauzeichner/innen",
    "Maschinenzeichner/innen",
    "Installationszeichner/innen",
    "Innenausbauzeichner/innen",
    "Elektrozeichner/innen",
    "Sonstige Technische Zeichnerberufe",
    "Technische Betriebsleiter/innen onA",
    "Betriebsfachleute",
    "Werkmeister/innen onA",
    "Sonstige technische Fachkräfte und Bediener/innen",
    "Energiemaschinisten/-maschinistinnen",
    "Baumaschinisten/-maschinistinnen uvB",
    "Holzmaschinisten/-maschinistinnen",
    "Sonstige Maschinisten/Maschinistinnen",
    "Heizer/innen",
    "Maschinen- und Anlagewärter/innen, Tankrevisoren/-revisorinnen",
    "Informatiker/innen, Analytiker/innen",
    "Programmierer/innen",
    "Informatikoperateure/-operatricen",
    "Webmasters/Webmistresses uvB",
    "Andere Berufe der Informatik",
    "Maurer/innen",
    "Betonbauer/innen, Zementierer/innen (Bau)",
    "Zimmerleute",
    "Strassenbauer/innen",
    "Pflästerer/Pflästerinnen",
    "Sprengfachleute, Tunnelbauer/innen, Mineure/Mineurinnen",
    "Baumeister/innen, Baupoliere/polierinnen uvB",
    "Sonstige Berufe des Bauhauptgewerbes",
    "Boden- und Plattenleger/innen",
    "Dachdecker/innen",
    "Verputzer/innen, Stuckateure/Stuckateurinnen",
    "Maler/innen, Tapezierer/innen",
    "Heizungs- und Lüftungsinstallateure/-installateurinnen",
    "Spengler/innen (Bau)",
    "Isolierer/innen",
    "Cheminée- und Kachelofenbauer/innen, Hafner/innen",
    "Glaser/innen",
    "Elektromonteure/-monteurinnen und -installateure/-installateurinnen",
    "Sanitärplaner/innen und -installateure/-installateurinnen",
    "Sonstige Berufe des Ausbaugewerbes",
    "Berufe des Bergbaus und der Förderung von Bodenschätzen",
    "Steinhauer/innen, Steinmetzen/-metzinnen",
    "Sonstige Steinbearbeiter/innen sowie -schleifer/innen",
    "Berufe der Baustoff- und Bausteinherstellung",
    "Einkäufer/innen",
    "Verkäufer/innen, Detailhandelsangestellte",
    "Kassiere/Kassiererinnen",
    "Verleger/innen, Buchhändler/innen",
    "Drogisten/Drogistinnen",
    "Tierhändler/innen",
    "Sonstige Verkaufsberufe",
    "Vertreter/innen, Handelsreisende",
    "Übrige Kaufleute und Händler/innen",
    "Werbefachleute",
    "PR-Fachleute",
    "Marketingfachleute",
    "Markt- und Meinungsforschungsfachleute",
    "Reisebüroangestellte",
    "Reiseleiter/innen, Fremdenführer/innen, Hostessen",
    "Andere Freizeit- und Tourismusfachleute",
    "Bücherexperten/-expertinnen und Revisoren/Revisorinnen",
    "Treuhänder/innen und Steuerberater/innen",
    "Übrige Dienstleistungskaufleute",
    "Vermittler/innen und Versteigerer/Versteigerinnen",
    "Verleiher/innen und Vermieter/innen",
    "Bahnhofvorstände und Bahnbetriebsdisponenten/-disponentinnen, -sekretäre/-sekretä-rinnen",
    "Streckenarbeiter/innen und Geleisemonteure/-monteurinnen",
    "Stellwerkbeamte/-beamtinnen",
    "Depot- und Rangierangestellte",
    "Zugsbegleiter/innen",
    "Lokomotiv- und Tramwagenführer/innen uvB",
    "Seilbahnberufe",
    "Übrige Berufe des Schienen- und Seilbahnverkehrs",
    "Berufe des Personentransports uvB",
    "Lastwagenchauffeure/-chauffeusen",
    "Sonstige Chauffeure/Chauffeusen",
    "Fahrlehrer/innen, Autoexperten/-expertinnen",
    "Schiffsführer/innen, Steuermänner/-frauen",
    "Matrosen/Matrosinnen und sonstige Berufe des Wasserverkehrs",
    "Flugkapitäne/-kapitäninnen, Piloten/Pilotinnen, Fluglehrer/innen",
    "Flugverkehrsleiter/innen",
    "Kabinenpersonal uvB",
    "Andere Luftverkehrsberufe",
    "Transportpersonal und Spediteure/Spediteurinnen",
    "Ausläufer/innen und Kuriere/Kurierinnen",
    "Übrige Transport- und Verkehrsberufe",
    "Posthalter/innen und Betriebssekretäre/-sekretärinnen der Post",
    "Betriebsassistenten/-assistentinnen der Post",
    "Zustellbeamte/-beamtinnen",
    "Teleoperateure/-operatricen und Telefonisten/Telefonistinnen",
    "Übrige Berufe des Postwesens",
    "Übrige Berufe des Fernmeldewesens",
    "Geschäftsführer/innen von Gaststätten und Hotels",
    "Empfangspersonal und Portiers",
    "Servicepersonal",
    "Etagen-, Wäscherei- und Economatpersonal",
    "Küchenpersonal",
    "Andere Berufe des Gastgewerbes",
    "Hauswirtschaftliche Betriebsleiter/innen",
    "Hauswirtschaftliche Angestellte",
    "Textilpfleger/innen, Chemisch-Reiniger/innen",
    "Bügler/innen, Wäscher/innen",
    "Hauswarte/-wartinnen, Raum- und Gebäudereiniger/innen",
    "Kaminfeger/innen",
    "Übrige Reinigungsberufe",
    "Bestattungsfachleute",
    "Berufe der Abfallentsorgung und -verwertung",
    "Übrige Berufe der öffentlichen Hygiene und Reinigung",
    "Coiffeure/Coiffeusen",
    "Kosmetiker/innen",
    "Berufe der Hand- und Fusspflege",
    "Übrige Berufe der Körperpflege",
    "Unternehmer/innen und Direktoren/Direktorinnen",
    "Leitende Beamte/Beamtinnen im öffentlichen Dienst",
    "Organisations- und Verbandsfunktionäre/-funktionärinnen",
    "Personalfachleute",
    "Mittleres Kader, onA",
    "Kaufmännische Angestellte sowie Büroberufe, wna",
    "Verwaltungsbeamte/-beamtinnen uvB",
    "Buchhalter/innen",
    "Immobilienfachleute und -verwalter/innen",
    "Import-Export-Fachleute",
    "Organisationsfachleute uvB",
    "Übrige Administrationsangestellte",
    "Berufe des Bankwesens, wna",
    "Versicherungsagenten/-agentinnen sowie -inspektoren/-inspektorinnen",
    "Berufe des Versicherungswesens, wna",
    "Polizei",
    "Wächter/innen, Aufseher/innen",
    "Berufsfeuerwehr, Zivilschutz",
    "Berufsmilitär uvB",
    "Übrige Berufe der Sicherheit",
    "Zoll und Grenzschutz",
    "Richter/innen und Staatsanwälte/-anwältinnen",
    "Gerichtsschreiber/innen",
    "Rechtsanwälte/-anwältinnen und Notare/Notarinnen",
    "Übrige Berufe des Rechtswesens",
    "Journalisten/Journalistinnen und Redaktoren/Redaktorinnen",
    "Korrektoren/Korrektorinnen und Lektoren/Lektorinnen",
    "Übersetzer/innen und Dolmetscher/innen",
    "Übrige Wort-, Bild- und Printmedienschaffende",
    "Bibliothekare/Bibliothekarinnen",
    "Archivare/Archivarinnen und Dokumentalisten/Dokumentalistinnen",
    "Konservatoren/Konservatorinnen und Museumsfachleute",
    "Berufe der Bühnen- und Filmausstattung",
    "Spielleiter/innen, Regisseure/Regisseurinnen, Produzenten/Produzentinnen",
    "Tonoperateure/-operatricen und -techniker/innen",
    "Kameraleute und Bildtechniker/innen",
    "Fotografen/Fotografinnen",
    "Sonstige Berufe der Bild- und Tonproduktion",
    "Musiker/innen, Komponisten/Komponistinnen und Dirigenten/Dirigentinnen",
    "Sänger/innen",
    "Schauspieler/innen",
    "Tänzer/innen, Tanzlehrer/innen und Choreographen/Choreographinnen",
    "Artisten/Artistinnen",
    "Photomodelle, Dressmen/Mannequins",
    "Andere darstellende Berufe",
    "Steinbildhauer/innen",
    "Kunstmaler/innen, -zeichner/innen",
    "Grafiker/innen und Plakatmaler/innen",
    "Designer/innen, Modeschöpfer/innen",
    "Andere künstlerische Gestalter/innen",
    "Edelmetallschmiede/-schmiedinnen",
    "Übrige Schmuckhersteller/innen",
    "Holzschnitzer/innen, -bildhauer/innen uvB",
    "Keramiker/innen, Töpfer/innen",
    "Keramik- und Glasmaler/innen",
    "Musikinstrumentenbauer/innen und -stimmer/innen",
    "Dekorateure/Dekorateurinnen und Dekorationsgestalter/innen",
    "Restauratoren/Restauratorinnen",
    "Innenarchitekten/-architektinnen, -dekorateure/-dekorateurinnen sowie -ausstat-ter/innen",
    "Andere Kunsthandwerker/innen",
    "Sozialarbeiter/innen",
    "Erzieher/innen",
    "Heim- und Krippenleiter/innen",
    "Andere Betreuerberufe",
    "Ordinierte Geistliche, Pfarrer/innen",
    "Sonstige Seelsorger/innen",
    "Angehörige geistlicher Orden",
    "Seelsorge- und Kulthelfer/innen",
    "Lehrer/innen an Hochschulen und höheren Fachschulen",
    "Wissenschaftliche Assistenten/Assistentinnen onA",
    "Mittelschullehrer/innen",
    "Oberstufenlehrer/innen",
    "Primarlehrer/innen",
    "Kindergärtner/innen uvB",
    "Berufs- und Fachschullehrer/innen",
    "Sonderschullehrer/innen, Heilpädagogen/-pädagoginnen",
    "Musik- und Gesangslehrer/innen",
    "Zeichen- und Werklehrer/innen",
    "Turn- und Sportlehrer/innen",
    "Erwachsenenbildner/innen",
    "Verschiedene Fachlehrer/innen und Kursleiter/innen",
    "Lehrer/innen und Instruktoren/Instruktorinnen onA",
    "Pädagogen/Pädagoginnen",
    "Berufe der Wirtschaftswissenschaften",
    "Soziologen/Soziologinnen, Politologen/Politologinnen",
    "Psychologen/Psychologinnen und Berufsberater/innen",
    "Philologen/Philologinnen",
    "Historiker/innen und Archäologen/Archäologinnen",
    "Andere Berufe der Geisteswissenschaften",
    "Biologen/Biologinnen",
    "Geographen/Geographinnen, Meteorologen/Meteorologinnen",
    "Chemiker/innen",
    "Mathematiker/innen, Statistiker/innen",
    "Physiker/innen",
    "Umweltschutzfachleute",
    "Andere Berufe der Naturwissenschaften",
    "Ärzte/Ärztinnen",
    "Medizinische Praxisassistenten/-assistentinnen, Arztgehilfen/-gehilfinnen",
    "Apotheker/innen",
    "Apothekenhelfer/innen",
    "Physiotherapeuten/-therapeutinnen, Ergotherapeuten/-therapeutinnen",
    "Nichtärztliche Psychotherapeuten/-therapeutinnen",
    "Heilpraktiker/innen",
    "Augenoptiker/innen",
    "Masseure/Masseurinnen",
    "Medizinisch-technische Assistenten/Assistentinnen",
    "Übrige Berufe der Therapie und der medizinischen Technik",
    "Medizinische Laboranten/Laborantinnen",
    "Zahnärzte/-ärztinnen",
    "Zahntechniker/innen",
    "Zahnarztgehilfen/-gehilfinnen",
    "Dentalhygieniker/innen",
    "Tierärzte/-ärztinnen",
    "Tiermedizinische Praxisassistenten/-assistentinnen, Tierarztgehilfen/-gehilfinnen",
    "Hebammen",
    "Kinderkrankenschwestern/-pfleger",
    "Psychiatriepfleger/innen",
    "Krankenschwestern/-pfleger",
    "Spitalgehilfen/-gehilfinnen, Hilfsschwestern/-pfleger",
    "Hauspflegerinnen/-pfleger, Gemeindekrankenschwestern/-pfleger",
    "Sonstige Krankenpflegeberufe",
    "Sportler/innen und Sporttrainer/innen",
    "Andere Berufe des Sports und der Unterhaltung",
    "Dienstleistungsberufe, wna",
    "Nicht einzuordnende Berufe der öffentlichen Verwaltung",
    "Sonstige nicht einzuordnende Berufe",
    "Arbeitskräfte mit nicht bestimmbarer Kader- oder Expertenfunktion",
    "Arbeitskräfte mit nicht bestimmbarer manueller Berufstätigkeit",
    "Arbeitskräfte mit nicht bestimmbarer nicht-manueller Berufstätigkeit",
    "Arbeitskräfte mit nicht bestimmbarer Berufstätigkeit",
    "Abschlüsse auf Sekundarstufe II",
    "Abschlüsse auf Tertiärstufe"
  ];
  $( ".occupation_tag" ).autocomplete({
   source: availableTags,
   autoFocus: true
  });
});


// jquery ui drag and drop sortable
$(function() {
    // init sortable
    $('ul.droptrue').sortable({
        connectWith: "ul",
        receive: function(event, ui) {
            var list = $(this);
            if (list.attr('id') == "sortable1") {
                $(ui.item).addClass("prioItem").removeClass("apartmentItem");
                if (list.children().length > 3) {
                    alert("Maximal 3 Wohnungen");
                    $(ui.sender).sortable('cancel');
                }
            } else if(list.attr('id') == "sortable2") {
                 $(ui.item).addClass("apartmentItem").removeClass("prioItem");
            }
        }
    });

    $('#sortable1, #sortable2').disableSelection();

    /*
    $( "ul.dropfalse" ).sortable({
        connectWith: "ul",
        dropOnEmpty: false,
    });
    */

});



function change_list () {
    $.post("/sortdatalist1.php", $('#sortable1').sortable("serialize"));
    $.post("/sortdatalist2.php", $('#sortable2').sortable("serialize"));
}


function reset_appartments() {
    //$("#sortable1 li").appendTo("#sortable2");
    //change_list();
}

function check_appartments(checkSum) {
    //if ( checkSum != 'firstCall' ) {
    //  $("#sortable1 li").appendTo("#sortable2");
    //  change_list();
    //}

    $(document).ready(function() {

        if($('#wgfilter1').is(':checked') == false && $('#wgfilter2').is(':checked') == false && $('#wgfilter3').is(':checked') == false && $('#wgfilter4').is(':checked') == false && $('#wgfilter5').is(':checked') == false && $('#wgfilter6').is(':checked') == false && $('#bdfilter1').is(':checked') == false) {
            $('li.apartmentItem').css('display','block');
        } else {
            $('li.apartmentItem').css('display','none');
        }

        if($('#bdfilter1').is(':checked')){
            $('li[building="302"]').css('display','block');
        }

        if($('#wgfilter1').is(':checked')){
            $('li[rooms="1"]').css('display','block');
            $('li[rooms="2"]').css('display','block');
            $('li[rooms="2.5"]').css('display','block');
        }

        if($('#wgfilter2').is(':checked')){
            $('li[rooms="3"]').css('display','block');
            $('li[rooms="3.5"]').css('display','block');
        }

        if($('#wgfilter3').is(':checked')){
            $('li[rooms="4.5"]').css('display','block');
            $('li[rooms="5.5"]').css('display','block');
        }

        if($('#wgfilter4').is(':checked')){
            $('li[rooms="6.5"]').css('display','block');
            $('li[rooms="7.5"]').css('display','block');
            $('li[rooms="8.5"]').css('display','block');
        }

        if($('#wgfilter5').is(':checked')){
            $('li[rooms="9.5"]').css('display','block');
            $('li[rooms="10.5"]').css('display','block');
            $('li[rooms="11.5"]').css('display','block');
            $('li[rooms="12.5"]').css('display','block');
            $('li[rooms="13"]').css('display','block');
            $('li[rooms="13.5"]').css('display','block');
        }

        if($('#wgfilter6').is(':checked')){
            $('li[subvention="1"]').css('display','block');
        }

    });
}


