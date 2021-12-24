<template>
    <div class="slide-item">

        <div class="slide-container">
            <div class="item handle">
                <slot/>
            </div>

            <div class="item slide-number">
                <span>Slide #{{ slide.id_slide }}</span>
            </div>

            <div class="item image-prev">
                <div :class="{ emptyimage : hasnotImage }" class="image-preview" :style="'background-image:url(' + shopDomain + slide.image + ')'">
                    <div v-if="hasTimer" class="timer"></div>
                </div>
                <div class="desc-mobile">
                    <span class="title">{{ slide.title }}</span>
                    <br><br>
                    <span class="description">{{ slide.description }}</span>
                </div>
            </div>

            <div class="item desc">
                <span class="title">{{ slide.title }}</span>
                <br><br>
                <span class="description">{{ slide.description }}</span>
            </div>

            <div class="item state">
                <span class="status">
                    <template v-if="slide.active">{{ translations.enabled }}</template>
                    <template v-else>{{ translations.disabled }}</template>
                </span>
                <el-switch @change="toggleSlideState()" v-model="slide.active"></el-switch>
            </div>

            <div @click="editSlide()" class="item action edit">
                <i class="material-icons cursor">edit</i>
            </div>

            <div @click="deleteSlide()" class="item action delete">
                <i class="material-icons cursor">delete</i>
            </div>
        </div>

    </div>
</template>

<script>
import { mapState } from 'vuex'

const shopDomain = baseUrl

export default {
    name: 'SlideItem',
    data () {
        return {
            shopDomain: shopDomain
        }
    },
    props: {
        slide: {
            type: Object,
            require: true
        }
    },
    methods: {
        editSlide () {
            this.$emit('editSlide', this.slide)
        },
        deleteSlide () {
            this.$emit('deleteSlide', this.slide)
        },
        toggleSlideState () {
            this.$emit('toggleSlideState', this.slide)
        }
    },
    computed: {
        ...mapState([
            'translations'
        ]),
        hasnotImage () {
            if (this.slide.image === '') {
                return true
            }
            return false
        },
        hasTimer () {
            if (this.slide.timer === '1') {
                return true
            }
            return false
        }
    }
}
</script>

<style scoped lang="scss">
.slide-container {
    display: flex;
    align-items: center;
}
.item {
    padding: 10px;
}
.image-prev {
    min-width: 370px;
    max-width: 370px;
}
@media only screen and (max-width: 1500px) {
    .desc {
        display: none;
        flex-grow: 1;
    }
    .desc-mobile {
        display: block !important;
    }
    .state {
        flex-grow: 1;
    }
}
.desc {
    flex-grow: 1;
}
.desc-mobile {
    margin-top: 10px;
    display: none;
}
.handle {
    color: #648593;
    font-size: 30px;
    max-width: 50px;
}
.slide-number {
    color: #648593;
    font-size: 16px;
    font-weight: 600;
    min-width: 125px;
    max-width: 125px;
}
.slide-item {
    padding: 10px;
    border-bottom: 1px solid #D9D9D9;
    background-color: white;
}
.image-preview {
    position: relative;
    height: 100px;
    width: 350px;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: 50% 50%;
}
.emptyimage {
    background-color: #EDEDED;
}
.emptyimage:after {
    position: absolute;
    font-family: 'Material icons';
    content: '\e439';
    left: 155px;
    top: 20px;
    font-size: 40px;
    color: white;
}
.timer {
    position:absolute;
    height: 100%;
    width: 50px;
    right: 0;
    background-color: rgba(0, 0, 0, 0.7);
}
.timer:after {
    position: absolute;
    font-family: 'Material icons';
    content: '\e190';
    color: white;
    font-size: 24px;
    left: 13px;
    top: 33px;
}
.cursor {
    cursor: pointer;
}
.state {
    min-width: 200px;
    text-align: center;
}
.status {
    color: #4A4A4A;
    font-size: 14px;
    margin-right: 15px;
}
.action {
    color: #648593;
    min-width: 50px;
    max-width: 50px;
}
.title {
    color: #4A4A4A;
    font-size: 15px;
    font-weight: 600;
}
.description {
    color: #6C868E;
    font-size: 12px;
}
</style>
