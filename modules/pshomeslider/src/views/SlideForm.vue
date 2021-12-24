<template>
    <el-row type="flex" justify="center">
        <el-col :xs="24" :sm="24" :md="24" :lg="20">

            <PsCard>

                <PsCardHeader>

                    <template slot="headerLeft">
                        <div v-if="!editMode"><i class="material-icons">add</i> {{ translations.createNewSlide }}</div>
                        <div v-else><i class="material-icons">edit</i> {{ translations.titleEditSlide }} #{{ this.$route.params.id }}</div>
                    </template>

                    <template slot="headerRight">
                        <el-select v-model="currentLanguage" value-key="iso_code" slot="append" class="language">
                            <el-option v-for="language in languages" :key="language.is_code" :label="language.iso_code" :value="language.iso_code"></el-option>
                        </el-select>
                    </template>

                </PsCardHeader>

                <PsCardBlock>

                    <el-row type="flex" justify="center">
                        <el-col :xs="18" :sm="18" :md="18" :lg="18">

                            <Alert v-if="Object.keys(errors).length > 0" type="danger">
                                <ul style="margin-top:0px;margin-bottom:0px">
                                    <li v-for="error in errors" :key="error">{{ error }}</li>
                                </ul>
                            </Alert>

                            <el-form v-if="!loading" ref="slideForm" :model="slideForm" label-width="250px">

                                <div class="title">{{ translations.uploadImage }}</div>

                                <el-form-item class="upload-image">
                                    <div class="dialog-help">
                                        <el-button type="text" @click="dialogHelp = true">{{ translations.dialogHelp }}</el-button>
                                    </div>
                                    <div class="box-upload" :class="{ dragover: dragover, filepreview: slideForm.file[currentLanguage].name }" @dragover="dragover = true" @dragleave="dragover = false" @drop="dragover = false" :style="'background: url(' + slideForm.filePreview[currentLanguage] + ')'">
                                        <input type="file" id="file" ref="file" v-on:change="handleFileUpload($event.target.files)"/>
                                        <div class="box-upload-content">
                                            <i class="material-icons upload">cloud_upload</i>
                                            <div class="upload-text">
                                                {{ translations.dropsFileHere }} <em>{{ translations.clickToUpload }}</em>
                                                <p class="upload-tip">{{ translations.recommandedImage }}</p>
                                            </div>
                                        </div>
                                        <div @click="handleRemove()" class="image-name" v-if="slideForm.file[currentLanguage].name">
                                            <span>{{ slideForm.file[currentLanguage].name }}</span>
                                        </div>
                                    </div>
                                </el-form-item>

                                <br>

                                <div class="title">{{ translations.textSettings }}</div>

                                <el-form-item :label="translations.slideTitle" class="input-size">
                                    <el-input v-model="slideForm.slideTitle[currentLanguage]"></el-input>
                                </el-form-item>

                                <el-form-item :label="translations.addYourText" class="input-size">
                                    <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" v-model="slideForm.slideText[currentLanguage]"></el-input>
                                </el-form-item>

                                <el-form-item :label="translations.textPosition" prop="textPosition">
                                    <el-radio v-model="slideForm.textPosition[currentLanguage]" :label="1" border>{{ translations.left }}</el-radio>
                                    <el-radio v-model="slideForm.textPosition[currentLanguage]" :label="2" border>{{ translations.centered }}</el-radio>
                                    <el-radio v-model="slideForm.textPosition[currentLanguage]" :label="3" border>{{ translations.right }}</el-radio>
                                </el-form-item>

                                <el-form-item :label="translations.textBackground" prop="textBackground">
                                    <el-switch v-model="slideForm.textBackground[currentLanguage]"></el-switch>
                                </el-form-item>

                                <div class="title">{{ translations.linkSettings }}</div>

                                <el-form-item :label="translations.redirectUrl" class="input-size">
                                    <el-input v-model="slideForm.redirectUrl[currentLanguage]"></el-input>
                                </el-form-item>

                                <el-form-item>
                                    <el-checkbox v-model="slideForm.openInNewTab[currentLanguage]">{{ translations.openInNewTab }}</el-checkbox>
                                </el-form-item>

                                <el-form-item :label="translations.addCallToAction">
                                    <el-switch v-model="slideForm.callToAction[currentLanguage]"></el-switch>
                                </el-form-item>

                                <transition name="fade">
                                    <el-form-item v-show="slideForm.callToAction[currentLanguage]" :label="translations.callToActionText" class="input-size">
                                        <el-input v-model="slideForm.callToActionText[currentLanguage]"></el-input>
                                    </el-form-item>
                                </transition>

                                <div class="title">{{ translations.timerSettings }}</div>

                                <el-form-item :label="translations.enableTimer">
                                    <el-switch v-model="slideForm.timer"></el-switch> <span class="tip-timer" v-if="slideForm.timer">{{ translations.tipTimer }}</span>
                                </el-form-item>

                                <transition name="fade">
                                    <el-form-item v-show="slideForm.timer" :label="translations.rangeDate" class="input-size">
                                        <el-date-picker class="datepicker-width"
                                            v-model="slideForm.availableDate"
                                            type="datetimerange"
                                            range-separator="To"
                                            start-placeholder="Start date"
                                            end-placeholder="End date">
                                        </el-date-picker>
                                    </el-form-item>
                                </transition>

                            </el-form>
                        </el-col>
                    </el-row>

                    <el-dialog :visible.sync="dialogHelp">
                        <div class="dialog-content">
                            <div class="responsive-display">
                                <img :src="imagePath+'/responsive_display.png'">
                                <div class="description">
                                    <p class="dialog-title">{{ translations.dialogResponsiveDisplay }}</p>
                                    <p class="dialog-label">{{ translations.dialogResponsiveDisplayText }}</p>
                                    <i class="material-icons">check</i>
                                </div>
                            </div>
                            <div class="static-display">
                                <img :src="imagePath+'/static_display.png'">
                                <div class="description">
                                    <p class="dialog-title">{{ translations.dialogStaticDisplay }}</p>
                                    <p class="dialog-label">
                                        {{ translations.dialogStaticDisplayText1 }}<br/>
                                        {{ translations.dialogStaticDisplayText2 }}
                                    </p>
                                    <i class="material-icons">close</i>
                                </div>
                            </div>
                        </div>
                    </el-dialog>

                    <el-dialog
                        :title="translations.exitDialogTitle"
                        :visible.sync="dialogCancelConfirmation"
                        width="30%">
                        <span>{{ translations.exitDialogText }}</span>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="dialogCancelConfirmation = false" type="info" plain>{{ translations.exitDialogCancel }}</el-button>
                            <el-button type="primary" @click="confirmExit()">{{ translations.exitDialogOk }}</el-button>
                        </span>
                    </el-dialog>

                </PsCardBlock>

                <PsCardFooter>

                    <template slot="footerLeft">
                        <el-button @click="cancel()" type="info" plain>{{ translations.cancel }}</el-button>
                    </template>

                    <template slot="footerRight">
                        <el-button v-if="!editMode" @click="createSlide()" type="primary">{{ translations.createSlide }}</el-button>
                        <el-button v-else @click="updateSlide()" type="primary">{{ translations.updateSlide }}</el-button>
                    </template>

                </PsCardFooter>

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
import Alert from '@/components/Alert.vue'
import { request, submitSlideForm } from '@/api/ajax.js'
import { mapState } from 'vuex'

export default {
    name: 'SlideForm',
    components: {
        PsCard,
        PsCardHeader,
        PsCardBlock,
        PsCardFooter,
        Alert
    },
    data: function () {
        return {
            editMode: false,
            dialogHelp: false,
            dragover: false,
            slideForm: {
                file: {},
                filePreview: {},
                timer: false,
                availableDate: {},
                slideTitle: {},
                slideText: {},
                textPosition: {},
                textBackground: {},
                redirectUrl: {},
                openInNewTab: {},
                callToAction: {},
                callToActionText: {}
            },
            loading: false,
            dialogCancelConfirmation: false,
            routeLeave: '',
            errors: {}
        }
    },
    methods: {
        handleFileUpload (file) {
            let reader = new FileReader()

            reader.addEventListener('load', event => {
                this.slideForm.filePreview[this.currentLanguage] = event.target.result
            })

            reader.readAsDataURL(file[0])

            // this.slideForm.file[this.currentLanguage] = Object.assign(Blob, this.slideForm[this.currentLanguage], file[0])
            this.slideForm.file[this.currentLanguage] = file[0]
        },
        handleRemove () {
            this.slideForm.file[this.currentLanguage] = ''
            this.slideForm.filePreview[this.currentLanguage] = ''
        },
        updateCurrentStep: function (currentStep) {
            this.currentStep = currentStep
        },
        createSlide () {
            submitSlideForm({
                action: 'CreateSlide',
                data: {
                    slide: this.slideForm
                }
            }).then(data => {
                if (data !== 1) {
                    this.errors = {}
                    this.errors = Object.assign({}, this.errors, data)
                    this.$notify({
                        title: this.translations.notificationError,
                        message: this.translations.notificationErrorSave,
                        type: 'error'
                    })
                } else {
                    this.$notify({
                        title: this.translations.notificationSuccess,
                        message: this.translations.notificationSuccessCreate,
                        type: 'success'
                    })
                    this.$router.push({
                        name: 'SlidesManager',
                        query: {
                            confirmExit: true
                        }
                    })
                }
            })
        },
        updateSlide () {
            submitSlideForm({
                action: 'UpdateSlide',
                data: {
                    idSlide: this.$route.params.id,
                    slide: this.slideForm
                }
            }).then(data => {
                if (data !== true) {
                    this.errors = {}
                    this.errors = Object.assign({}, this.errors, data)
                    this.$notify({
                        title: this.translations.notificationError,
                        message: this.translations.notificationErrorSave,
                        type: 'error'
                    })
                } else {
                    this.$notify({
                        title: this.translations.notificationSuccess,
                        message: this.translations.notificationSuccessUpdate,
                        type: 'success'
                    })
                    this.$router.push({
                        name: 'SlidesManager',
                        query: {
                            confirmExit: true
                        }
                    })
                }
            })
        },
        cancel: function () {
            this.$router.push('/slidesManager')
        },
        confirmExit: function () {
            this.$router.push({
                path: this.routeLeave,
                query: {
                    confirmExit: true
                }
            })
        }
    },
    computed: {
        ...mapState([
            'languages',
            'translations',
            'imagePath'
        ]),
        currentLanguage: {
            get () {
                return this.$store.state.fieldLanguage
            },
            set (value) {
                this.$store.commit('updateFieldLanguage', value)
            }
        }
    },
    created () {
        var availableLanguage = {}

        _.forEach(this.languages, (lang, key) => {
            _.forEach(this.slideForm, (field, key) => {
                if (typeof availableLanguage[key] !== 'object') {
                    availableLanguage[key] = {}
                }

                switch (key) {
                case 'callToAction':
                case 'openInNewTab':
                case 'textBackground':
                    availableLanguage[key][lang.iso_code] = false
                    break
                case 'textPosition':
                    availableLanguage[key][lang.iso_code] = 2
                    break
                default:
                    availableLanguage[key][lang.iso_code] = ''
                    break
                }
            })
        })

        this.slideForm = Object.assign({}, this.slideForm, availableLanguage)

        if (this.$route.params.edit) {
            this.loading = true
            this.editMode = true

            request({
                action: 'GetSlide',
                data: {
                    idSlide: this.$route.params.id
                }
            }).then(data => {
                this.slideForm = data
                this.loading = false
            })
        }
    },
    beforeRouteLeave (to, from, next) {
        this.dialogCancelConfirmation = true
        this.routeLeave = to.path

        if (to.query.confirmExit) {
            next()
        } else {
            next(false)
        }
    }
}
</script>

<style scoped lang="scss">
.tip-timer {
    color: #cd9321;
    font-style: italic;
    margin-left: 15px;
}
.datepicker-width {
    width: 500px !important;
}
.title {
    color: #606266;
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 20px;
}
.box-upload {
    border: 2px dashed #d9d9d9;
    border-radius: 4px;
    outline-offset: -10px;
    // padding: 10px 10px;
    position: relative;
    min-height: 300px;
    cursor: pointer;
    background-size: cover !important;
    background-repeat: no-repeat !important;
    background-position: 50% 50% !important;
}
.box-upload:hover {
    border-color: #3ed2f0;
}
.dialog-help {
    position: absolute;
    top: -35px;
    right: 0;
}
.filepreview {
    border: unset;
    border-radius: unset;
}
.filepreview > .box-upload-content {
    visibility: hidden;
    transition: background-color .5s ease-out;
}
.filepreview:hover > .box-upload-content {
    visibility: visible;
    background-color: rgba(0, 0, 0, 0.7);
}
.filepreview:hover > .box-upload-content .upload-text {
    color: white !important;
}
.filepreview:hover > .box-upload-content .upload {
    color: white !important;
}
.filepreview:hover > .box-upload-content .upload-text > .upload-tip {
    color: white !important;
}
.filepreview:hover .image-name {
    background-color: #EFEFEF;
}
.filepreview:hover .image-name:after {
    font-family: 'Material icons';
    color: #4A4A4A;
    content: "\e5cd";
    transform: rotate(-180deg) !important;
    transition: all .5s !important;
}
.dragover {
    background-color: rgba(32, 159, 255, 0.06) !important;
}
.box-upload > input {
    opacity: 0; /* invisible but it's there! */
    width: 100%;
    height: 300px;
    position: absolute;
    cursor: pointer;
}
.box-upload-content {
    text-align: center;
    height: 300px;
}
.box-upload-content .upload {
    font-size: 76px;
    color: #c0c4cc;
    margin: 80px 0 16px;
    line-height: 50px;
}
.box-upload-content .upload-text {
    line-height: 20px;
}
.box-upload-content .upload-text em {
    font-style: normal;
    color: #3ed2f0;
}
.upload-tip {
    color: #4A4A4A;
    font-size: 12px;
}
.image-name {
    color: #4A4A4A;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    padding-left: 10px;
    padding-right: 10px;
    position: absolute;
    left: 50%;
    transform: translate(-50%);
    line-height: 30px !important;
    margin-top: 10px;
    cursor: pointer;
}
.image-name:after {
    font-family: 'Material icons';
    content: "\e5ca";
    font-size: 18px;
    margin-left: 5px;
    color: #3ed2f0;
    transform: rotate(0deg) !important;
    transition: all .5s !important;
}
.image-name:hover {
    background-color: #EFEFEF;
}
.image-name:hover:after {
    font-family: 'Material icons';
    color: #4A4A4A;
    content: "\e5cd";
    transform: rotate(-180deg) !important;
    transition: all .5s !important;
}
.dialog-content {
    width: 80%;
    margin: auto;
}
.dialog-content img {
    width: 100%;
}
.responsive-display .description {
    position: relative;
    border-top: 1px solid rgba(151, 151, 151, 0.3);
    margin-top: 15px;
    padding-top: 15px;
}
.responsive-display .description .dialog-title {
    color: #25B9D7;
    font-size: 20px;
    font-weight: 600;
    line-height: 27px;
    margin-left: 70px;
}
.responsive-display .description .dialog-label {
    font-size: 14px;
    color: #4A4A4A;
    margin-left: 70px;
}
.static-display .description {
    position: relative;
    border-top: 1px solid rgba(151, 151, 151, 0.3);
    margin-top: 15px;
    padding-top: 15px;
}
.static-display .description .dialog-title {
    color:#D0021B;
    font-size: 20px;
    font-weight: 600;
    line-height: 27px;
    margin-left: 70px;
}
.static-display .description .dialog-label {
    font-size: 14px;
    color: #4A4A4A;
    margin-left: 70px;
}
.responsive-display .material-icons {
    position: absolute;
    left: 0;
    top: 25px;
    font-size: 50px;
    color: #25B9D7;
}
.static-display .material-icons {
    position: absolute;
    left: 0;
    top: 25px;
    font-size: 50px;
    color: #d0021b;
}
.fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>

<style>
.el-dialog {
    width: 60%;
    margin-top: 5vh !important;
}
.el-checkbox__inner {
    width: 18px !important;
    height: 18px !important;
}
.el-checkbox__inner::after {
    border: 2px solid #fff !important;
    border-left: 0 !important;
    border-top: 0 !important;
    width: 3px !important;
    height: 10px !important;
    left: 6px !important;
    top: 1px !important;
}
.el-date-editor .el-range-input, .el-date-editor .el-range-separator {
    height: unset !important;
}
</style>
