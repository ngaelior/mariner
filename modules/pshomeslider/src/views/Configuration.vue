<template>
    <div>
        <el-row type="flex" justify="center">
            <el-col :xs="24" :sm="24" :md="24" :lg="20">
                <PsCard>
                    <PsCardHeader>
                        <template slot="headerLeft">
                            <i class="material-icons">settings</i> {{ translations.titleGeneralSettings }}
                        </template>
                    </PsCardHeader>

                    <PsCardBlock>

                        <el-row type="flex" justify="center">
                            <el-col :xs="18" :sm="18" :md="18" :lg="18">
                                <el-form v-loading="loading" ref="formGeneralSettings" :model="formGeneralSettings" label-width="250px">

                                    <el-form-item :label="translations.transitionEffect" prop="transitionEffect">
                                        <el-radio v-model="formGeneralSettings.transitionEffect" label="1" border>{{ translations.slide }}</el-radio>
                                        <el-radio v-model="formGeneralSettings.transitionEffect" label="2" border>{{ translations.fade }}</el-radio>
                                        <!-- <el-radio v-model="formGeneralSettings.transitionEffect" label="3" border>{{ translations.easeOut }}</el-radio> -->
                                    </el-form-item>

                                    <el-form-item :label="translations.transitionSpeed" prop="transitionSpeed">
                                        <el-select v-model="formGeneralSettings.transitionSpeed">
                                            <el-option :label="translations.slow + ' (9000ms)'" value="9000"></el-option>
                                            <el-option :label="translations.normal + ' (5000ms)'" value="5000"></el-option>
                                            <el-option :label="translations.speed + ' (2000ms)'" value="2000"></el-option>
                                        </el-select>
                                    </el-form-item>

                                    <br>

                                    <el-form-item :label="translations.pauseOnHover" prop="pauseOnHover">
                                        <el-switch v-model="formGeneralSettings.pauseOnHover"></el-switch>
                                    </el-form-item>

                                    <el-form-item :label="translations.loopForever" prop="loopForever">
                                        <el-switch v-model="formGeneralSettings.loopForever"></el-switch>
                                    </el-form-item>

                                    <br>

                                    <el-form-item :label="translations.navigationStyle" prop="navigationType">
                                        <el-radio v-model="formGeneralSettings.navigationType" label="1" border>{{ translations.arrows }}</el-radio>
                                        <el-radio v-model="formGeneralSettings.navigationType" label="2" border>{{ translations.dots }}</el-radio>
                                        <el-radio v-model="formGeneralSettings.navigationType" label="3" border>{{ translations.both }}</el-radio>
                                    </el-form-item>

                                </el-form>
                            </el-col>
                        </el-row>

                    </PsCardBlock>

                    <PsCardFooter>
                        <template slot="footerRight">
                            <el-button @click="saveSettings()" type="primary">{{ translations.saveForm }}</el-button>
                        </template>
                    </PsCardFooter>
                </PsCard>
            </el-col>
        </el-row>

        <el-row type="flex" justify="center">
            <el-col :xs="24" :sm="24" :md="24" :lg="20">
                <PsCard>
                    <PsCardHeader>
                        <template slot="headerLeft">
                            <div>
                                <i class="material-icons">palette</i> {{ translations.titleVisualSettings }}
                            </div>
                        </template>
                    </PsCardHeader>

                    <PsCardBlock>

                        <el-row type="flex" justify="center">
                            <el-col :xs="18" :sm="18" :md="18" :lg="18">
                                <el-form v-loading="loading" ref="formVisualSettings" :model="formVisualSettings" label-width="250px">

                                    <div class="title">{{ translations.titles }}</div>

                                    <el-form-item :label="translations.titleTypography" prop="titleTypography">
                                        <el-select v-model="formVisualSettings.titleTypography" :class="formVisualSettings.titleTypography.replace(/\s/g,'')">
                                            <el-option v-for="font in fonts" :style="'font-family:'+font+'!important'" :key="font" :label="font" :value="font"></el-option>
                                        </el-select>
                                    </el-form-item>

                                    <el-form-item :label="translations.titleSize" prop="titleSize">
                                        <el-slider class="slider-width" v-model="formVisualSettings.titleSize" :min="8" :max="72" show-input></el-slider>
                                    </el-form-item>

                                    <div class="title">{{ translations.paragraphs }}</div>

                                    <el-form-item :label="translations.paragraphTypography" prop="paragraphTypography">
                                        <el-select v-model="formVisualSettings.paragraphTypography" :class="formVisualSettings.paragraphTypography.replace(/\s/g,'')">
                                            <el-option v-for="font in fonts" :style="'font-family:'+font+'!important'" :key="font" :label="font" :value="font"></el-option>
                                        </el-select>
                                    </el-form-item>

                                    <el-form-item :label="translations.paragraphSize" prop="paragraphSize">
                                        <el-slider class="slider-width" v-model="formVisualSettings.paragraphSize" :min="8" :max="72" show-input></el-slider>
                                    </el-form-item>

                                    <div class="title">{{ translations.navigation }}</div>

                                    <el-form-item :label="translations.navigationElementsColor" prop="navigationElementsColor">
                                        <verte v-model="formVisualSettings.navigationElementsColor"></verte>
                                    </el-form-item>

                                </el-form>
                            </el-col>
                        </el-row>

                    </PsCardBlock>

                    <PsCardFooter>
                        <template slot="footerRight">
                            <el-button @click="saveSettings()" type="primary">{{ translations.saveForm }}</el-button>
                        </template>
                    </PsCardFooter>
                </PsCard>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import PsCard from '@/components/panel/PsCard.vue'
import PsCardHeader from '@/components/panel/PsCardHeader.vue'
import PsCardBlock from '@/components/panel/PsCardBlock.vue'
import PsCardFooter from '@/components/panel/PsCardFooter.vue'
import Alert from '@/components/Alert.vue'
import Verte from '@/components/colorPicker/components/Verte.vue'
import { request } from '@/api/ajax.js'
import { mapState } from 'vuex'

const fontList = JSON.parse(fonts)

export default {
    name: 'home',
    components: {
        PsCard,
        PsCardHeader,
        PsCardBlock,
        PsCardFooter,
        Alert,
        Verte
    },
    data: function () {
        return {
            fonts: fontList,
            formGeneralSettings: {
                transitionEffect: '',
                transitionSpeed: '',
                pauseOnHover: false,
                loopForever: false,
                navigationType: ''
            },
            formVisualSettings: {
                titleTypography: '',
                titleSize: 0,
                paragraphTypography: '',
                paragraphSize: 0,
                navigationElementsColor: ''
            },
            loading: true
        }
    },
    methods: {
        getSettings () {
            request({
                action: 'GetConfiguration'
            }).then(response => {
                this.formGeneralSettings = response.formGeneralSettings
                this.formVisualSettings = response.formVisualSettings
                this.loading = false
            }).catch(error => {
                console.log(error)
                this.$notify({
                    title: this.translations.notificationError,
                    message: this.translations.notificationErrorAjax,
                    type: 'error'
                })
            })
        },
        saveSettings () {
            request({
                action: 'SaveConfiguration',
                data: {
                    formGeneralSettings: JSON.stringify(this.formGeneralSettings),
                    formVisualSettings: JSON.stringify(this.formVisualSettings)
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
                        message: this.translations.notificationSaveConfigurationSuccess,
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
        }
    },
    created () {
        this.getSettings()
    },
    computed: {
        ...mapState([
            'translations'
        ])
    }
}
</script>

<style scoped lang="scss">
.slider-width {
    width: 400px;
}
.title {
    color: #606266;
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 20px;
}
</style>
<style>
.Roboto>.el-input>.el-input__inner {
    font-family: Roboto !important;
}
.Montserrat>.el-input>.el-input__inner {
    font-family: Mont serrat !important;
}
.Rubik>.el-input>.el-input__inner {
    font-family: Rubik !important;
}
.Arvo>.el-input>.el-input__inner {
    font-family: Arvo !important;
}
.Oswald>.el-input>.el-input__inner {
    font-family: Oswald !important;
}
.Oxygen>.el-input>.el-input__inner {
    font-family: Oxygen !important;
}
.AbrilFatface>.el-input>.el-input__inner {
    font-family: Abril Fatface !important;
}
.ConcertOne>.el-input>.el-input__inner {
    font-family: Concert One !important;
}
.Spectral>.el-input>.el-input__inner {
    font-family: Spectral !important;
}
.Ubuntu>.el-input>.el-input__inner {
    font-family: Ubuntu !important;
}
.NotoSans>.el-input>.el-input__inner {
    font-family: Noto Sans !important;
}
</style>
