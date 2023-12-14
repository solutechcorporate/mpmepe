import { createRouter, createWebHistory } from 'vue-router'

import Accueil_page from '@/views/Accueil_Page'
import Article_page from '@/views/Article_Page'
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
       }
    ]
})

export default router;