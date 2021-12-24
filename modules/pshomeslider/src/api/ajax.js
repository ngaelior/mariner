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

import axios from 'axios'
import _ from 'lodash'

const ajax = axios.create({
    baseURL: adminAjaxController
})

export function request (params) {
    const form = new FormData()
    form.append('ajax', true)
    form.append('action', params.action)

    _.forEach(params.data, function (value, key) {
        form.append(key, value)
    })

    return ajax.post('', form)
        .then(res => res.data)
        .catch(error => {
            console.log(error)
        })
}

export function submitSlideForm (params) {
    const form = new FormData()

    form.append('ajax', true)
    form.append('action', params.action)

    if (params.data.idSlide) {
        form.append('idSlide', params.data.idSlide)
    }

    let result = {}

    result['availableDate'] = params.data.slide.availableDate
    result['timer'] = params.data.slide.timer

    _.forEach(params.data.slide, function (value, key) {
        _.forEach(value, function (value, lang) {
            if (typeof result[lang] !== 'object') {
                result[lang] = {}
            }

            if (value instanceof File) {
                form.append('file_' + lang, value)
            } else {
                if (key !== 'filePreview' && key !== 'availableDate' && key !== 'timer') {
                    result[lang][key] = value
                }
            }
        })
    })

    form.append('formSlide', JSON.stringify(result))

    return ajax.post('', form)
        .then(res => res.data)
        .catch(error => {
            console.log(error)
        })
}

export function getFaq (moduleKey, psVersion, isoCode) {
    return ajax.post('http://api.addons.prestashop.com/request/faq/' + moduleKey + '/' + psVersion + '/' + isoCode)
        .then(res => res.data)
        .catch(error => {
            console.log(error)
        })
}
