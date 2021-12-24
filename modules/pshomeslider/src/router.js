/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

import Vue from 'vue'
import Router from 'vue-router'

import Configuration from '@/views/Configuration.vue'
import SlidesManager from '@/views/SlidesManager.vue'
import SlideForm from '@/views/SlideForm.vue'
import Help from '@/views/Help.vue'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            redirect: '/configuration'
        },
        {
            path: '/configuration',
            name: 'Configuration',
            component: Configuration
        },
        {
            path: '/slidesManager',
            name: 'SlidesManager',
            component: SlidesManager
        },
        {
            path: '/slidesManager/new',
            name: 'NewSlide',
            component: SlideForm
        },
        {
            path: '/slidesManager/:edit/:id',
            name: 'EditSlide',
            component: SlideForm
        },
        {
            path: '/help',
            name: 'Help',
            component: Help
        }
    ]
})
