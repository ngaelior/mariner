// import { getRandomColor } from 'color-fns'
// import { newArray } from './utils'
// import { cpus } from 'os'

const MAX_COLOR_HISTROY = 5
let Vue
let store

export function initStore (_Vue, opts) {
    if (store) {
        return store
    }

    opts = opts || {}
    const { recentColors, onRecentColorsChange } = opts

    Vue = _Vue
    store = new Vue({
        data: () => ({
            // recentColors: recentColors || newArray(5, getRandomColor)
            recentColors: JSON.parse(localStorage.getItem('verte')) || [],
            defaultColor: {}
        }),
        methods: {
            addRecentColor (newColor) {
                if (this.recentColors.includes(newColor)) {
                    return
                }

                if (this.recentColors.length >= MAX_COLOR_HISTROY) {
                    this.recentColors.shift()
                }

                this.recentColors.push(newColor)
                if (onRecentColorsChange) {
                    onRecentColorsChange(this.recentColors)
                }

                localStorage.setItem('verte', JSON.stringify(this.recentColors))
            }
        },
        computed: {
            remainingColors () {
                return MAX_COLOR_HISTROY - this.recentColors.length
            }
        }
    })

    return store
};
