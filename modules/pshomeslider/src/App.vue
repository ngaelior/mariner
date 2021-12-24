<template>
    <div id="app">
        <Menu>
            <MenuItem route="/configuration">{{ translations.menuConfiguration }}</MenuItem>
            <MenuItem route="/slidesManager">{{ translations.menuSliderManager }}</MenuItem>
            <MenuItem route="/help">{{ translations.menuHelp }}</MenuItem>
        </Menu>
        <router-view/>
    </div>
</template>

<script>
import Menu from '@/components/menu/Menu.vue'
import MenuItem from '@/components/menu/MenuItem.vue'
import { mapState } from 'vuex'

export default {
    name: 'home',
    components: {
        Menu,
        MenuItem
    },
    computed: {
        ...mapState([
            'translations'
        ])
    }
}
</script>
