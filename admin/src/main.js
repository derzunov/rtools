import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

export const objects = ref([])

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas } from '@fortawesome/free-solid-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'

// Add all icons to the library, so we can use it in our app
library.add(fas, far, fab)
createApp( App )
    .use( router )
    .component("font-awesome-icon", FontAwesomeIcon)
    .mount( '#app' )
