<template>
    <div class="sidebar" style="margin-top: 5%;">
        <ul class="contenant" style="margin-top: 13%;">
            <li class="général">Général</li>
            <a class="nav-link" :to="{ name: 'Accueil_Page' }">
                <li class="menus"><i class="fas fa-home-alt"></i> Accueil</li>
            </a>
            <router-link class="nav-link" :to="{ name: 'Article_Page' }">
                <li class="menus"> <i class="far fa-file-alt"></i> Article</li>
            </router-link>
            <li class="menus"><i class="far fa-play-circle"></i> Média</li>
            <li class="général">Documents</li>
            <li class="menus"> <i class="far fa-film"></i> Cartégorie</li>
            <li class="menus"> <i class="fas fa-file-alt"></i> Document</li>
            <li class="général">Contact</li>
            <li class="menus"> <i class="far fa-address-card"></i> Type de demande</li>
            <li class="menus"> <i class="fas fa-file"></i> Demande</li>
            <li class="menus"> <i class="far fa-address-book"></i> Contact</li>
            <li class="général">Ministère</li>
            <li class="menus"> <i class="far fa-id-badge"></i> Ministère</li>
            <li class="menus"> <i class="fas fa-building"></i> Direction</li>
            <li class="menus"> <i class="fas fa-user-tie"></i> Dirigeant</li>
            <li class="général">Apparence</li>
            <li class="menus"> <i class="far fa-bars"></i> Menu</li>
            <li class="menus"> <i class="fas fa-chevron-down"></i> Sous-menu</li>
            <li class="menus"> <i class="far fa-file-alt"></i> Page</li>
            <li class="général">Réglages</li>
            <li class="menus"> <i class="far fa-copy"></i> Copyright</li>
            <li class="menus"> <i class="far fa-share-square"></i> Social-media</li>
            <li class="général">Utilisateurs</li>
            <li class="menus"> <i class="far fa-save"></i> Sauvegarde</li>
            <li class="menus"><i class="fas fa-book"></i> Guide d'utilisation</li>
        </ul>
    </div>
    <div class="navbar">
        <img class="logo_mpeme" src="@/assets/mpeme.png" alt="">
        <router-link class="nav-link" :to="{ name: 'Article_Page' }">
            <div class="rechercher"><i class="fas fa-search white-icon"></i> Rechercher</div>
        </router-link>
        <div>
            <select id="choix" name="choix" style=" margin-left: -30%;">
                <option value="option1">Votre compte</option>
                <option value="option1">Profil</option>
                <option value="option2">Paramètres</option>
                <option value="option3">Documentation</option>
            </select>
        </div>
        <div>
            <div class="rechercher" style=" margin-left: -20%;"><i class="fas fa-power-off"></i> Se déconnecter
            </div>
        </div>
    </div>
        <div
            style="position: absolute;top:15%; right: 0; width: 80%; display: flex; justify-content: start; align-items: start; overflow-y: auto;">
            <div style="width: 100%; display: flex; justify-content: center;">
                <div style="width: 100%;">
                    <p></p>
                    <hr style="margin-top: 40%;">
                    <p style="margin-left: 5%;">Copyright © 2023 Ministère des petites et moyennes entrepries Tout droit
                        reservé.</p>
                    <div style="margin-left: 70%; margin-top: -5%;"><img class="logo2" src="@/assets/logo2.png" alt="">
                    </div>
                    <div class="container">
                        <div class="rectangle green"></div>
                        <div class="rectangle yellow"></div>
                        <div class="rectangle red"></div>
                    </div>

                </div>
            </div>
        <!-- </div> -->
    </div>
</template>
<script>
</script>
  
<style scoped>
/* Reset des styles par défaut */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600&display=swap');

.logo_mpeme {
    width: 250px;
}

/* Styles pour la sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 90%;
    width: 250px;
    background-color: white;
    color: white;
    box-shadow: 1px 5px 5px 0px rgba(0, 0, 0, 0.5);
    overflow-y: auto;
}

.sidebar ul {
    list-style: none;
    padding: 0px;
}

.sidebar li {
    padding: 8px;
}

/* Styles pour la navbar */
.navbar {
    position: fixed;
    background: linear-gradient(to right, #093E73, #627946);
    color: white;
    padding: 10px;
    width: 100%;
    /* Pour que la navbar soit au-dessus de la sidebar */

}

.général {
    color: white;
    font-weight: bold;
    font-size: 18px;
    background: linear-gradient(to right, #093E73, #627946);
    font-family: "inter";
}

.menus {
    color: black;
    font-family: "inter";
}

.rechercher:hover {
    color: #ecac16;
    cursor: pointer
}

select {
    background-color: transparent;
    border: transparent;
    color: white;
    font-weight: bold;
    font-family: "inter";
    cursor: pointer;
}

option {
    color: #ecac16;
}

.logo2 {
    width: 150px;
}

.rectangle {
    width: 325px;
    height: 10px;
    display: inline-block;
}

.green {
    background-color: green;
}

.yellow {
    background-color: yellow;
}

.red {
    background-color: red;
}

router-link {
    cursor: pointer;
}</style>
  