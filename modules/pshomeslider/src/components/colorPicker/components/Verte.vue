<template>
    <div class="verte" :class="{ 'verte--loading': isLoading }">
        <button class="verte__guide" ref="guide" type="button" v-if="!menuOnly" :style="`color: ${currentColor}; fill: ${currentColor};`" @click="toggleMenu">
            <slot>
                <svg class="verte__icon" width="400" height="100" viewBox="0 0 24 24">
                    <pattern id="checkerboard" width="6" height="6" patternUnits="userSpaceOnUse" fill="FFF">
                        <rect fill="#7080707f" x="0" width="3" height="3" y="0"></rect><rect fill="#7080707f" x="3" width="3" height="3" y="3"></rect>
                    </pattern>
                    <rect width="56" height="24" fill="url(#checkerboard)" x="-16" rx="2" ry="2"></rect>
                    <rect width="56" height="24" x="-16" rx="2" ry="2"></rect>

                    <!-- <rect width="300" height="100" fill="url(#checkerboard)"></rect>
                    <rect width="300" height="100"></rect> -->
                </svg>
            </slot>
        </button>
        <div class="verte__menu-origin" :class="[`verte__menu-origin--${menuPosition}`, { 'verte__menu-origin--static': menuOnly, 'verte__menu-origin--active': isMenuActive }]">
            <div class="verte__menu" ref="menu" tabindex="-1" :style="`transform: translate(${delta.x}px, ${delta.y}px)`">
                <!-- <button class="verte__close" v-if="!menuOnly" @click="closeMenu" type="button">
                    <svg class="verte__icon verte__icon--small" viewBox="0 0 24 24">
                        <title>Close Icon</title>
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"></path>
                    </svg>
                </button> -->
                <div class="verte__draggable" v-if="draggable &amp;&amp; !menuOnly" @mousedown="handleMenuDrag" @touchstart="handleMenuDrag">
                  <div class="drag-bar"></div>
                </div>
                <div v-if="enableDefaultColors" class="verte__inputs">
                    <div class="verte__default" ref="recent">
                        <a class="verte__default-color" role="button" href="#" v-for="clr in defaultColors" :style="`color: ${clr}`" @click.prevent="selectColor(clr)"></a>
                    </div>
                </div>
                <Picker :mode="picker" :alpha="alpha" v-model="currentColor"></Picker>
                <div class="verte__controller">
                    <Slider v-if="enableAlpha" :gradient="[`rgba(${rgb.red}, ${rgb.green}, ${rgb.blue}, 0)`, `rgba(${rgb.red}, ${rgb.green}, ${rgb.blue}, 1)`]" :min="0" :max="1" :step="0.01" :editable="false" v-model="alpha"></Slider>
                    <template v-if="rgbSliders">
                        <Slider :gradient="[`rgb(0,${rgb.green},${rgb.blue})`, `rgb(255,${rgb.green},${rgb.blue})`]" v-model="rgb.red"></Slider>
                        <Slider :gradient="[`rgb(${rgb.red},0,${rgb.blue})`, `rgb(${rgb.red},255,${rgb.blue})`]" v-model="rgb.green"></Slider>
                        <Slider :gradient="[`rgb(${rgb.red},${rgb.green},0)`, `rgb(${rgb.red},${rgb.green},255)`]" v-model="rgb.blue"></Slider>
                    </template>
                    <div class="verte__inputs">
                        <!-- <button class="verte__model" @click="switchModel" type="button">{{currentModel}}</button> -->
                        <template v-if="currentModel === 'hsl'"><input class="verte__input" @change="inputChanged($event, 'hue')" :value="hsl.hue" type="number" max="360" min="0"/>
                            <input class="verte__input" @change="inputChanged($event, 'sat')" :value="hsl.sat" type="number" min="0" max="100"/>
                            <input class="verte__input" @change="inputChanged($event, 'lum')" :value="hsl.lum" type="number" min="0" max="100"/>
                        </template>
                        <template v-if="currentModel === 'rgb'"><input class="verte__input" @change="inputChanged($event, 'red')" :value="rgb.red" type="number" min="0" max="255" />
                            <input class="verte__input" @change="inputChanged($event, 'green')" :value="rgb.green" type="number" min="0" max="255"/>
                            <input class="verte__input" @change="inputChanged($event, 'blue')" :value="rgb.blue" type="number" min="0" max="255" />
                        </template>
                        <template v-if="currentModel === 'hex'">
                            <input class="verte__input" @change="inputChanged($event, 'hex')" :value="hex" type="text"/>
                        </template>
                        <!-- <button class="verte__submit" @click="submit" type="button">
                            <title>Submit Icon</title>
                            <svg class="verte__icon" viewBox="0 0 24 24">
                                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path>
                            </svg>
                        </button> -->
                    </div>
                    <div class="verte__recent" ref="recent" v-if="recentColors">
                        <a class="verte__submit" type="button" @click="submit"></a>
                        <a class="verte__recent-color" role="button" href="#" v-for="clr in $_verteStore.recentColors" :style="`color: ${clr}`" @click.prevent="selectColor(clr)"></a>
                        <a class="verte__recent-color" role="button" v-for="clr in $_verteStore.remainingColors" style="color: #F1F1F1"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { toRgb, toHex, toHsl, isValidColor, alpha } from 'color-fns'
import Picker from './Picker.vue'
import Slider from './Slider.vue'
import { initStore } from '../store'
import { isElementClosest, warn, makeListValidator, getEventCords } from '../utils'

export default {
  name: 'Verte',
  components: {
    Picker,
    Slider
  },
  props: {
    picker: {
      type: String,
      default: 'square',
      validator: makeListValidator('picker', ['wheel', 'square'])
    },
    value: {
      type: String,
      default: '#000'
    },
    model: {
      type: String,
      default: 'hex',
      validator: makeListValidator('model', ['rgb', 'hex', 'hsl'])
    },
    display: {
      type: String,
      default: 'picker',
      validator: makeListValidator('display', ['picker', 'widget'])
    },
    menuPosition: {
      type: String,
      default: 'right',
      validator: makeListValidator('menuPosition', ['top', 'bottom', 'left', 'right', 'center'])
    },
    recentColors: {
      default: true
    },
    enableAlpha: {
      type: Boolean,
      default: false
    },
    rgbSliders: {
      type: Boolean,
      default: false
    },
    draggable: {
      type: Boolean,
      default: true
    },
    enableDefaultColors: {
      type: Boolean,
      default: true
    },
    defaultColors: {
      type: Array,
      default: function () {
        return [
          '#4DD0E1',
          '#41A85F',
          '#FBA026',
          '#E14938',
          '#A38F84',
          '#263238'
        ]
      }
    }
  },
  data: () => ({
    isMenuActive: true,
    isLoading: true,
    rgb: toRgb('#000'),
    hex: toHex('#000'),
    hsl: toHsl('#000'),
    delta: { x: 0, y: 0 },
    currentModel: ''
  }),
  computed: {
    $_verteStore () {
      // Should return the store singleton instance.
      return initStore();
    },
    currentColor: {
      get () {
        if (!this[this.model] && process.env.NODE_ENV !== 'production') {
          warn(`You are using a non-supported color model: "${this.model}", the supported models are: "rgb", "hsl" and "hex".`);
          return `rgb(0, 0, 0)`;
        }

        return this[this.model].toString();
      },
      set (val) {
        this.selectColor(val);
      }
    },
    alpha: {
      get () {
        if (!this[this.model]) {
          return 1;
        }

        if (isNaN(this[this.model].alpha)) {
          return 1;
        }

        return this[this.model].alpha;
      },
      set (val) {
        this[this.model].alpha = val
        this.selectColor(this[this.model]);
      }
    },
    menuOnly () {
      return this.display === 'widget';
    }
  },
  watch: {
    value (val, oldVal) {
      if (val === oldVal || val === this.currentColor) return;

      // value was updated externally.
      this.selectColor(val);
    },
    rgb: {
      handler (val) {
        this.hex = toHex(val.toString());
        this.$emit('input', this.currentColor);
      },
      deep: true
    }
  },
  methods: {
    selectColor (color, muted = false) {
      if (!isValidColor(color)) return;

      this.rgb = toRgb(color);
      this.hex = toHex(color);
      this.hsl = toHsl(color);

      if (muted) return;
      this.$emit('input', this.currentColor);
    },
    switchModel () {
      const models = ['hex', 'rgb', 'hsl'];
      const indx = models.indexOf(this.currentModel);
      this.currentModel = models[indx + 1] || models[0];
    },
    handleMenuDrag (event) {
      if (event.button === 2) return;
      event.preventDefault();

      const lastMove = Object.assign({}, this.delta);
      const startPosition = getEventCords(event);

      const handleDragging = (evnt) => {
        window.requestAnimationFrame(() => {
          const endPosition = getEventCords(evnt);

          this.delta.x = lastMove.x + endPosition.x - startPosition.x;
          this.delta.y = lastMove.y + endPosition.y - startPosition.y;
        });
      };
      const handleRelase = () => {
        document.removeEventListener('mousemove', handleDragging);
        document.removeEventListener('mouseup', handleRelase);
        document.removeEventListener('touchmove', handleDragging);
        document.removeEventListener('touchup', handleRelase);
      };
      document.addEventListener('mousemove', handleDragging);
      document.addEventListener('mouseup', handleRelase);
      document.addEventListener('touchmove', handleDragging);
      document.addEventListener('touchup', handleRelase);
    },
    submit () {
      this.$emit('beforeSubmit', this.currentColor);
      this.$_verteStore.addRecentColor(this.currentColor);
      this.$emit('input', this.currentColor);
      this.$emit('submit', this.currentColor);
    },
    inputChanged (event, value) {
      const el = event.target;
      if (this.currentModel === 'hex') {
        this.selectColor(el.value);
        return;
      }
      const normalized = Math.min(Math.max(el.value, el.min), el.max);
      this[this.currentModel][value] = normalized;
      this.selectColor(this[this.currentModel]);
    },
    toggleMenu () {
      if (this.isMenuActive) {
        this.closeMenu();
        return;
      }
      this.openMenu();
    },
    closeMenu () {
      this.isMenuActive = false;
      document.removeEventListener('mousedown', this.closeCallback);
      this.$emit('close', this.currentColor);
    },
    openMenu () {
      this.isMenuActive = true;
      this.closeCallback = (evnt) => {
        if (
          !isElementClosest(evnt.target, this.$refs.menu) &&
          !isElementClosest(evnt.target, this.$refs.guide)
        ) {
          this.closeMenu();
        }
      };
      document.addEventListener('mousedown', this.closeCallback);
    }
  },
  beforeCreate () {
    // initialize the store early, _base is the vue constructor.
    initStore(this.$options._base);
  },
  // When used as a target for Vue.use
  install (Vue, opts) {
    initStore(Vue, opts);
    Vue.component('Verte', this); // install self
  },
  created () {
    this.selectColor(this.value || '#000', true);
    this.currentModel = this.model;
  },
  mounted () {
    // give sliders time to
    // calculate its visible width
    this.$nextTick(() => {
      this.isLoading = false;
      if (this.menuOnly) return;
      this.isMenuActive = false;
    });
  }
};
</script>

<style lang="sass">
@import '../sass/variables';

$dot-size: 2px;
$dot-space: 4px;

.verte
  position: relative

.verte--loading
  opacity: 0
.verte__guide
  width: 76px
  height: 36px
  padding: 0
  border: 0
  background: transparent

  &:focus
    outline: 0

  svg
    width: 100%
    height: 100%
    border: 3px solid #DCDCDC
    border-radius: 3px
    fill: inherit

.verte__menu
  flex-direction: column
  justify-content: center
  align-items: stretch
  width: 252px
  background-color: $white
  will-change: transform
  border-radius: 4px
  border: 1px solid #CCCCCC;
  box-shadow: 0 2px 10px 0 rgba($black, 0.15)
  &:focus
    outline: none

.verte__menu-origin
  display: none
  position: absolute
  z-index: 9999
  &--active
    display: flex
  &--static
    position: static
    z-index: initial
  &--top
    bottom: 50px
  &--bottom
    top: 50px
  &--right
    left: 90px
    top: 0px
  &--left
    left: 0
  &--center
    position: fixed
    top: 0
    left: 0
    width: 100vw
    height: 100vh
    justify-content: center
    align-items: center
    background-color: rgba($black, 0.1)

  &:focus
    outline: none
.verte__controller
  padding: 0 20px 10px

.verte__recent
  display: flex
  flex-wrap: wrap
  justify-content: flex-start
  align-items: center
  width: 100%

  &-color
    margin: 4px
    width: 27px
    height: 27px
    border-radius: 50%
    background-color: $white
    box-shadow: 0 2px 4px rgba($black, 0.1)
    background-image: $checkerboard
    background-size: 6px 6px
    background-position: 0 0, 3px -3px, 0 3px, -3px 0px
    overflow: hidden
    &:after
      content: ''
      display: block
      width: 100%
      height: 100%
      background-color: currentColor

.verte__default
  display: flex
  flex-wrap: wrap
  justify-content: flex-start
  align-items: center
  width: 100%
  padding: 2px 20px

  &-color
    margin: 4px
    width: 27px
    height: 27px
    border-radius: 50%
    background-color: $white
    box-shadow: 0 2px 4px rgba($black, 0.1)
    background-image: $checkerboard
    background-size: 6px 6px
    background-position: 0 0, 3px -3px, 0 3px, -3px 0px
    overflow: hidden
    &:after
      content: ''
      display: block
      width: 100%
      height: 100%
      background-color: currentColor

.verte__value
  padding: 0.6em
  width: 100%
  border: $border solid $gray
  border-radius: $borderRadius 0 0 $borderRadius
  text-align: center
  font-size: $fontTiny
  -webkit-appearance: none
  -moz-appearance: textfield
  &:focus
    outline: none
    border-color: $blue
.verte__icon
  width: 20px
  height: 20px
  &--small
    width: 12px
    height: 12px
.verte__input
  padding: 5px
  margin: 0 3px
  color: #111111 !important
  font-weight: 600
  font-size: 16px !important
  min-width: 0
  text-align: center
  border-radius: 2px !important
  border: 0px solid #EDEDED !important
  background-color: #EDEDED !important
  &::-webkit-inner-spin-button,
  &::-webkit-outer-spin-button
    -webkit-appearance: none
    margin: 0
  appearance: none
  -moz-appearance: textfield

.verte__inputs
  display: flex
  font-size: 16px
  margin-bottom: 5px
.verte__draggable
  width: 25px
  height: 4px
  border-radius: 4px
  background-color: #9BB3BA
  margin-top: 4px
  margin-bottom: 4px
  margin-left: auto
  margin-right: auto
  cursor: grab

.verte__model,
.verte__submit
  position: relative
  cursor: pointer
  margin: 4px
  width: 27px
  height: 27px
  border-radius: 50%
  border: 2px solid #6C868E
  background-color: #fff
  -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1)
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1)
  overflow: hidden
  &:after
    font-family: 'Material icons'
    content: '\e145'
    position: absolute
    left: 0px
    top: -8px
    font-size: 24px
    color: #6C868E
    display: block
    width: 100%
    height: 100%
  &:hover
    background-color: #6C868E
    fill: $blue
    color: $blue
  &:hover:after
    color: #fff

.verte__close
  position: absolute
  top: 1px
  right: 1px
  z-index: 1
  display: flex
  justify-content: center
  align-items: center
  padding: 4px
  cursor: pointer
  border-radius: 50%
  border: 0
  transform: translate(50%, -50%)
  background-color: rgba($black, 0.4)
  fill: $white
  outline: none
  box-shadow: 1px 1px 1px rgba($black, 0.2)
  &:hover
    background-color: rgba($black, 0.6)

</style>
