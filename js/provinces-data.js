// ===== DONNÉES DES PROVINCES, VILLES, COMMUNES ET TERRITOIRES RDC =====

const provincesData = {
    "BAS UELE": {
        villes: ["Buta"],
        communes: {
            "Buta": ["Aketi", "Ango", "Baye", "Bili", "Monga", "Ndu", "Bambesa", "Titule", "Zobia", "Poko", "Buta"]
        },
        territoires: []
    },
    "EQUATEUR": {
        villes: ["Mbandaka"],
        communes: {
            "Mbandaka": ["Commune de Mbandaka", "Commune de Wangata", "Commune d'Ikenge", "Commune de Bolumbu"]
        },
        territoires: []
    },
    "HAUT KATANGA": {
        villes: ["Lubumbashi", "Likasi"],
        communes: {
            "Lubumbashi": ["Commune de Lubumbashi", "Commune de Kampemba", "Commune de Katuba", "Commune de Ruashi", "Commune de Kenya", "Commune de Kamalondo", "Commune Annexe"],
            "Likasi": ["Commune de Panda", "Commune de Kikula", "Commune de Likasi", "Commune de Shituru"]
        },
        territoires: ["Kipushi", "Sakania", "Kasenga", "Mitwaba", "Pweto", "Kambove"]
    },
    "HAUT LOMAMI": {
        villes: ["Kamina"],
        communes: {
            "Kamina": ["Dimayi", "Kamina (ville)", "Sobongo"]
        },
        territoires: ["Bukama", "Luena", "Kabondo-Dianda", "Malemba-Nkulu", "Kaniama", "Kabongo", "Mulongo"]
    },
    "HAUT UELE": {
        villes: ["Isiro"],
        communes: {
            "Isiro": ["Isiro"]
        },
        territoires: ["Rungu", "Niangara", "Dungu", "Faradje", "Wamba", "Watsa"]
    },
    "ITURI": {
        villes: ["Bunia"],
        communes: {
            "Bunia": ["Nyakasanza", "Mbunya / Shari", "Lumumba"]
        },
        territoires: ["Aru", "Djugu", "Irumu", "Mahagi", "Mambasa"]
    },
    "KASAI": {
        villes: ["Tshikapa"],
        communes: {
            "Tshikapa": ["Kanzala", "Mabondo", "Mbumba", "Dibumba 1", "Dibumba 2"]
        },
        territoires: []
    },
    "KASAI CENTRAL": {
        villes: ["Kananga"],
        communes: {
            "Kananga": ["Kananga", "Katoka", "Lukonga", "Ndesha", "Nganza", "Demba", "Dibaya", "Dimbelenge", "Kazumba", "Luiza"]
        },
        territoires: []
    },
    "KASAI ORIENTAL": {
        villes: ["Mbujimayi"],
        communes: {
            "Mbujimayi": ["Bipemba", "Dibindi", "Diulu", "Kanshi", "Muya"]
        },
        territoires: ["Kabeya Kamwanga", "Katanda", "Lupatapata", "Miabi", "Tshilenge"]
    },
    "KINSHASA": {
        villes: ["Kinshasa"],
        communes: {
            "Kinshasa": ["Bandalungwa", "Bumbu", "Kalamu", "Kasa-Vubu", "Makala", "Ngiri-Ngiri", "Selembao", "Barumbu", "Gombe", "Kinshasa", "Kintambo", "Lingwala", "Mont-Ngafula", "Ngaliema", "Kisenso", "Lemba", "Limete", "Matete", "Ngaba", "Kimbanseke", "Maluku", "Masina", "Ndjili", "Nsele"]
        },
        territoires: []
    },
    "KONGO CENTRAL": {
        villes: ["Matadi", "Boma", "Muanda"],
        communes: {
            "Matadi": ["Matadi", "Mvuzi", "Nzanza"],
            "Boma": ["Boma", "Kalamu", "Nzadi"]
        },
        territoires: ["Kasangulu", "Kimvula", "Lukula", "Luozi", "Madimba", "Mbanza-Ngungu", "Moanda", "Seke-Banza", "Songololo", "Tshela"]
    },
    "KWANGO": {
        villes: ["Kenge"],
        communes: {
            "Kenge": ["Ville de Kenge"]
        },
        territoires: ["Feshi", "Kahemba", "Kasongo-Lunda", "Kenge", "Popokabaka"]
    },
    "KWILU": {
        villes: ["Kikwit", "Bandundu"],
        communes: {
            "Kikwit": ["Lukolela", "Lukemi", "Mzinda", "Samba"],
            "Bandundu": ["Basoko", "Disasi (avec Wombali)", "Mayoyo"]
        },
        territoires: []
    },
    "LOMAMI": {
        villes: ["Kabinda", "Mwene-Ditu", "Bondoyi"],
        communes: {
            "Kabinda": ["Kabinda", "Congo", "Mudishi"],
            "Mwene-Ditu": ["Mwene-Ditu", "Bondoyi", "Musadi"]
        },
        territoires: ["Kabinda", "Ngandajika", "Luilu", "Kamiji", "Lubao"]
    },
    "LUALABA": {
        villes: ["Kasaji", "Kolwezi"],
        communes: {
            "Kolwezi": ["Manika", "Dilala"],
            "Kasaji": ["Nyakasanza", "Commune de Mbunya", "Commune de Shari"]
        },
        territoires: ["Dilolo", "Kapanga", "Lubudi", "Mutshatsha", "Sandoa"]
    },
    "MAI-NDOMBE": {
        villes: ["Inongo"],
        communes: {
            "Inongo": ["Inongo"]
        },
        territoires: ["Bolobo", "Kiri", "Kutu", "Kwamouth", "Mushie", "Oshwe", "Yumbi"]
    },
    "MANIEMA": {
        villes: ["Kindu"],
        communes: {
            "Kindu": ["Kasuku", "Mikelenge", "Alunguli"]
        },
        territoires: ["Kambare", "Kailo", "Kasongo", "Kibombo", "Lubutu", "Pangi", "Punia", "Kampene"]
    },
    "MONGALA": {
        villes: ["Lisala", "Bumba"],
        communes: {
            "Lisala": ["Commune de Lisala", "Commune de Bundji"],
            "Bumba": ["Commune de Bumba"]
        },
        territoires: ["Bongandanga", "Bumba"]
    },
    "NORD-KIVU": {
        villes: ["Goma", "Beni", "Butembo"],
        communes: {
            "Goma": ["Commune de Goma", "Commune de Karisimbi"],
            "Beni": ["Commune de Beni", "Commune de Bungulu", "Commune de Ruwenzori", "Commune de Beu"],
            "Butembo": ["Commune de Bulengera", "Commune de Kimemi", "Commune de Mususa", "Commune de Vulamba"]
        },
        territoires: ["Beni", "Lubero", "Masisi", "Nyiragongo", "Rutshuru", "Walikale"]
    },
    "NORD-UBANGI": {
        villes: ["Gbadolite"],
        communes: {
            "Gbadolite": ["Ville de Gbadolite"]
        },
        territoires: ["Bosobolo", "Businga", "Mobayi-Mbongo", "Yakoma"]
    },
    "SANKURU": {
        villes: ["Lusambo", "Lodja"],
        communes: {
            "Lusambo": ["Kabondo", "Lupembe", "Lusambo", "Tusuanganyi"],
            "Lodja": ["Lodja"]
        },
        territoires: ["Katako-Kombe", "Kole", "Lomela", "Lubefu", "Lusambo"]
    },
    "SUD-KIVU": {
        villes: ["Bukavu", "Uvira", "Baraka", "Kamituga"],
        communes: {
            "Bukavu": ["Ibanda", "Kadutu", "Bagira"],
            "Uvira": ["Kalundu", "Mulongwe"],
            "Baraka": ["Kalundja", "Kasongelera", "Katanga"],
            "Kamituga": ["Mobale", "Bitanda"]
        },
        territoires: ["Fizi", "Idjwi", "Kabare", "Kalehe", "Mwenga", "Shabunda", "Uvira", "Walungu"]
    },
    "SUD-UBANGI": {
        villes: ["Gemena", "Zongo"],
        communes: {
            "Gemena": ["Mont Gila", "Gbazubu", "Labo", "Lac-Ntumba"],
            "Zongo": ["Nzulu", "Wango"]
        },
        territoires: ["Libenge", "Budjala", "Kungu"]
    },
    "TANGANYIKA": {
        villes: ["Kalemie", "Kongolo", "Manono", "Moba/Kaoze"],
        communes: {
            "Kalemie": ["Kalemie", "Lukuga", "Kampinda"],
            "Kongolo": ["Kabinda", "Kangoyi", "Lualaba"],
            "Manono": ["Lukushi", "Kanteba", "Kaulu-Minono", "Kitotolo"],
            "Moba/Kaoze": ["Kirungu", "Moba port", "Murumbi"]
        },
        territoires: ["Kalemie", "Kabalo", "Kongolo", "Manono", "Moba", "Nyunzu"]
    },
    "TSHOPO": {
        villes: ["Kisangani"],
        communes: {
            "Kisangani": ["Kisangani", "Kabondo", "Lubunga", "Tshopo", "Mangobo", "Makiso"]
        },
        territoires: ["Bafwasende", "Banalia", "Basoko", "Isangi", "Opala", "Ubundu", "Yahuma"]
    },
    "TSHUAPA": {
        villes: ["Boende"],
        communes: {
            "Boende": ["Commune de Boende", "Commune de Tshuapa"]
        },
        territoires: ["Befale", "Boende", "Bokungu", "Djolu", "Ikela", "Monkoto"]
    }
};

// Fonction pour obtenir les provinces triées
function getProvinces() {
    return Object.keys(provincesData).sort();
}

// Fonction pour obtenir les villes d'une province
function getVillesByProvince(province) {
    if (provincesData[province]) {
        return provincesData[province].villes.sort();
    }
    return [];
}

// Fonction pour obtenir les communes d'une ville
function getCommunesByVille(province, ville) {
    if (provincesData[province] && provincesData[province].communes[ville]) {
        return provincesData[province].communes[ville].sort();
    }
    return [];
}

// Fonction pour obtenir les territoires d'une province
function getTerritoiresByProvince(province) {
    if (provincesData[province]) {
        return provincesData[province].territoires.sort();
    }
    return [];
}
