<template>
    <el-row type="flex" justify="center">
        <el-col :xs="24" :sm="24" :md="24" :lg="20">

            <PsCard>

                <PsCardHeader>

                    <template slot="headerLeft">
                        <i class="material-icons">slideshow</i> {{ translations.titleSliderManager }}
                    </template>

                    <template slot="headerRight">
                        <el-button v-if="imageCanBeUpload" @click="createNewSlide()" type="primary">{{ translations.createNewSlide }}</el-button>
                    </template>

                </PsCardHeader>

                <PsCardBlock class="override-card-block">

                    <Alert v-if="!imageCanBeUpload" class="message-margin" type="danger">{{ translations.imageFolderIsNotWritable }}</Alert>

                    <Promised v-if="imageCanBeUpload" :promise="getSlides()">

                        <div slot="pending">
                            <Alert class="message-margin" type="info">{{ translations.loadingSlides }}</Alert>
                        </div>

                        <div slot-scope="data">
                            <SlickList v-if="slides.length > 0" v-model="slides" :useDragHandle="true" @input="updateSlidePosition($event)" helperClass="handle-active" class="drag-background">
                                <SlickItem v-for="(item, index) in slides" :index="index" :key="index">
                                    <SlideItem
                                        @deleteSlide="deleteSlide($event)"
                                        @editSlide="editSlide($event)"
                                        @toggleSlideState="toggleSlideState($event)"
                                        :slide="item">
                                        <i v-handle class="material-icons handle">drag_indicator</i>
                                    </SlideItem>
                                </SlickItem>
                            </SlickList>
                            <Alert v-else class="message-margin" type="info">{{ translations.noSlides }}</Alert>
                        </div>

                        <div slot="rejected" slot-scope="error">
                            <Alert class="message-margin" type="danger">{{ error.message }}</Alert>
                        </div>

                    </Promised>

                </PsCardBlock>

            </PsCard>

        </el-col>
    </el-row>
</template>

<script>
import _ from 'lodash'
import PsCard from '@/components/panel/PsCard.vue'
import PsCardHeader from '@/components/panel/PsCardHeader.vue'
import PsCardBlock from '@/components/panel/PsCardBlock.vue'
import PsCardFooter from '@/components/panel/PsCardFooter.vue'
import SlideItem from '@/components/SlideItem.vue'

import { Promised } from 'vue-promised'
import { SlickList, SlickItem, HandleDirective } from 'vue-slicksort'

import Alert from '@/components/Alert.vue'
import { request } from '@/api/ajax.js'
import { mapState } from 'vuex'

export default {
    name: 'home',
    components: {
        PsCard,
        PsCardHeader,
        PsCardBlock,
        PsCardFooter,
        SlickList,
        SlickItem,
        Promised,
        Alert,
        SlideItem
    },
    directives: { handle: HandleDirective },
    data: function () {
        return {
            imageCanBeUpload: true,
            slides: ''
        }
    },
    methods: {
        checkImageUploadRights () {
            request({
                action: 'CheckImageUploadRights'
            }).then(response => {
                this.imageCanBeUpload = response
            })
        },
        deleteSlide (slide) {
            request({
                action: 'DeleteSlide',
                data: {
                    id_slide: slide.id_slide
                }
            }).then(response => {
                if (response !== 'success') {
                    this.$notify({
                        title: this.translations.notificationError,
                        message: this.translations.notificationSaveConfigurationError,
                        type: 'error'
                    })
                } else {
                    let slideIndex = _.findIndex(this.slides, o => { return o === slide })
                    this.slides.splice(slideIndex, 1)

                    this.$notify({
                        title: this.translations.notificationSuccess,
                        message: this.translations.notificationDeleteSuccess,
                        type: 'success'
                    })
                }
            }).catch(error => {
                console.log(error)
                this.$notify({
                    title: this.translations.notificationError,
                    message: this.translations.notificationErrorAjax,
                    type: 'error'
                })
            })
        },
        editSlide (slide) {
            this.$router.push('/slidesManager/edit/' + slide.id_slide)
        },
        toggleSlideState (slide) {
            request({
                action: 'ToggleState',
                data: {
                    id_slide: slide.id_slide
                }
            }).then(response => {
                if (response !== 'success') {
                    this.$notify({
                        title: this.translations.notificationError,
                        message: this.translations.notificationSaveConfigurationError,
                        type: 'error'
                    })
                } else {
                    this.$notify({
                        title: this.translations.notificationSuccess,
                        message: this.translations.notificationUpdateSuccess,
                        type: 'success'
                    })
                }
            }).catch(error => {
                console.log(error)
                this.$notify({
                    title: this.translations.notificationError,
                    message: this.translations.notificationErrorAjax,
                    type: 'error'
                })
            })
        },
        updateSlidePosition (event) {
            request({
                action: 'UpdatePosition',
                data: {
                    position: JSON.stringify(event)
                }
            }).then(response => {
                if (response !== 'success') {
                    this.$notify({
                        title: this.translations.notificationError,
                        message: this.translations.notificationUpdatePositionError,
                        type: 'error'
                    })
                } else {
                    this.$notify({
                        title: this.translations.notificationSuccess,
                        message: this.translations.notificationUpdatePositionSuccess,
                        type: 'success'
                    })
                }
            }).catch(error => {
                console.log(error)
                this.$notify({
                    title: this.translations.notificationError,
                    message: this.translations.notificationErrorAjax,
                    type: 'error'
                })
            })
        },
        createNewSlide () {
            this.$router.push('/slidesManager/new')
        },
        getSlides: function () {
            return request({ action: 'GetSlides' }).then(response => {
                this.slides = response
                return response
            })
        }
    },
    created () {
        this.checkImageUploadRights()
    },
    computed: {
        ...mapState([
            'translations'
        ])
    }
}
</script>

<style scoped lang="scss">
.override-card-block {
    padding: unset !important;
}
.message-margin {
    margin: 20px;
}
.handle {
    cursor: url('https://www.google.com/intl/en_ALL/mapfiles/openhand.cur'), all-scroll;
    cursor: -webkit-grab;
    cursor: -moz-grab;
    cursor: -o-grab;
    cursor: -ms-grab;
    cursor: grab;
}
.handle-active {
    // -webkit-box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.06);
    // box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.06);
    box-shadow: 0 6px 10px 0 rgba(0,0,0,.14), 0 1px 18px 0 rgba(0,0,0,.12), 0 3px 5px -1px rgba(0,0,0,.2);
    pointer-events: unset !important;
    z-index: 99999;
}
.handle-active .handle {
    cursor: url('https://www.google.com/intl/en_ALL/mapfiles/closedhand.cur'), all-scroll;
    cursor: -webkit-grabbing;
    cursor: -moz-grabbing;
    cursor: -o-grabbing;
    cursor: -ms-grabbing;
    cursor: grabbing !important;
}
.drag-background {
    background-color: #f3f3f3;
}
</style>
