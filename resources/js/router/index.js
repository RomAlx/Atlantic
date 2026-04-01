import { createRouter, createWebHistory } from 'vue-router';
import AboutView from '../views/AboutView.vue';
import CatalogCategoryView from '../views/CatalogCategoryView.vue';
import CatalogProductView from '../views/CatalogProductView.vue';
import CatalogView from '../views/CatalogView.vue';
import ContactsView from '../views/ContactsView.vue';
import HomeView from '../views/HomeView.vue';
import NotFoundView from '../views/NotFoundView.vue';
import SearchView from '../views/SearchView.vue';
const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'home', component: HomeView },
        { path: '/about', name: 'about', component: AboutView },
        { path: '/catalog', name: 'catalog', component: CatalogView },
        { path: '/catalog/:categorySlug', name: 'category', component: CatalogCategoryView },
        { path: '/catalog/:categorySlug/:productSlug', name: 'product', component: CatalogProductView },
        { path: '/contacts', name: 'contacts', component: ContactsView },
        { path: '/search', name: 'search', component: SearchView },
        { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFoundView },
    ],
    scrollBehavior() {
        return { top: 0 };
    },
});

export default router;
