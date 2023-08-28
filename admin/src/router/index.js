import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'main',
    component: () => import( /* webpackChunkName: "urls" */ '../views/DashboardView.vue' )
  },
  {
    path: '/ipm',
    name: 'urls',
    component: () => import( /* webpackChunkName: "urls" */ '../views/UrlsView.vue' )
  },
  {
    path: '/ipm/add/:currentUrl?',
    name: 'add',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import( /* webpackChunkName: "add" */ '../views/AddView.vue' )
  },
  {
    path: '/ipm/edit/:urlId/:itemId',
    name: 'edit',
    component: () => import( /* webpackChunkName: "edit" */ '../views/EditView.vue' )
  },
  {
    path: '/variants/edit/',
    name: 'Variants edit',
    component: () => import( /* webpackChunkName: "variants.edit" */ '../views/EditVariantsView.vue' )
  },
  {
    path: '/home',
    name: 'home',
    component: HomeView
  },
  {
    path: '/news/:passedUrlId',
    name: 'news',
    component: () => import( /* webpackChunkName: "news" */ '../views/NewsView.vue' )
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import( /* webpackChunkName: "about" */ '../views/AboutView.vue' )
  },
  {
    path: '/prices',
    name: 'prices',
    component: () => import( /* webpackChunkName: "prices" */ '../views/price-lists/PriceLists.vue' )
  },
  {
    path: '/prices/add',
    name: 'create price list',
    component: () => import( /* webpackChunkName: "prices" */ '../views/price-lists/AddPriceList.vue' )
  },
  {
    path: '/prices/content/:passedFileName',
    name: 'edit price-list content',
    component: () => import( /* webpackChunkName: "prices" */ '../views/price-lists/EditContent.vue' )
  },
  {
    path: '/prices/struct/:passedFileName',
    name: 'edit price-list Struct',
    component: () => import( /* webpackChunkName: "prices" */ '../views/price-lists/EditStruct.vue' )
  },
  {
    path: '/prices/show/:passedFileName',
    name: 'show price-list content',
    component: () => import( /* webpackChunkName: "prices" */ '../views/price-lists/ShowContent.vue' )
  },
  {
    path: '/prices/groups/show',
    name: 'Show groups with populated subgroups',
    component: () => import( /* webpackChunkName: "groups" */ '../views/price-lists/ShowGroups.vue' )
  },
  {
    path: '/prices/groups/edit',
    name: 'Edit groups',
    component: () => import( /* webpackChunkName: "groups.edit" */ '../views/price-lists/EditGroups.vue' )
  },
  {
    path: '/prices/subgroups/edit',
    name: 'Show subgroups',
    component: () => import( /* webpackChunkName: "subgroups.edit" */ '../views/price-lists/EditSubgroups.vue' )
  },
  {
    path: '/prices/changed',
    name: 'Show changed 1s codes',
    component: () => import( /* webpackChunkName: "ones.changed" */ '../views/price-lists/ShowChanged.vue' )
  },
  {
    path: '/calculators',
    name: 'Calculators page',
    component: () => import( /* webpackChunkName: "calculators" */ '../views/CalculatorsView.vue' )
  },
  {
    path: '/semantic/add',
    name: 'Add semantic page',
    component: () => import( /* webpackChunkName: "semantic" */ '../views/AddSemanticView.vue' )
  },
]

const router = createRouter( {
  history: createWebHashHistory( '/tools/admin/' ), // TODO: Move url to configs
  routes
} )

export default router
