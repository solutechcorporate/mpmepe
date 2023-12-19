import { createRouter, createWebHistory } from 'vue-router'

import Accueil_page from '@/views/Accueil_Page'
import Article_page from '@/views/Article_Page'
import Formulaire_page from '@/views/Formulaire_Page'
const router = createRouter({
    history: createWebHistory(),
    routes: [
       {
        path: '/',
        name: 'Accueil_Page',
        component:Accueil_page
       },
       {
        path: '/Article_Page',
        name: 'Article_Page',
        component:Article_page
       },
       {
        path: '/Formulaire_Page',
        name: 'Formulaire_Page',
        component:Formulaire_page
       }
    ]
})

export default router;