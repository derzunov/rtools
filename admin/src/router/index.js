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
    name: 'variants edit',
    component: () => import( /* webpackChunkName: "variants-edit" */ '../views/EditVariantsView.vue' )
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
]

const router = createRouter( {
  history: createWebHashHistory( '/tools/admin/' ), // TODO: Move to configs
  routes
} )

export default router
