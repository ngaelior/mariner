<template>
    <el-row type="flex" justify="center">
        <el-col :xs="24" :sm="24" :md="24" :lg="20">
            <PsCard>
                <PsCardHeader>
                    <template slot="headerLeft">
                        <i class="material-icons">help</i> {{ translations.menuHelp }}
                    </template>
                </PsCardHeader>

                <PsCardBlock id="faq">

                    <el-row justify="center" :gutter="50">

                        <el-col :xs="16" :sm="16" :md="16" :lg="16">

                            <div class="module-tips">
                                <div class="module-image">
                                    <img :src="baseUrl + '/modules/pshomeslider/logo.png'" alt="">
                                </div>
                                <div class="module-text">
                                    {{ translations.moduleAllowsYou }} :
                                    <ul>
                                        <li>{{ translations.helpTip1 }}</li>
                                        <li>{{ translations.helpTip6 }}</li>
                                        <li>{{ translations.helpTip2 }}</li>
                                        <li>{{ translations.helpTip3 }}</li>
                                        <li>{{ translations.helpTip4 }}</li>
                                        <li>{{ translations.helpTip5 }}</li>
                                    </ul>
                                </div>
                            </div>

                            <h3 v-if="faq && faq.categories.length !== 0">FAQ</h3>
                            <template v-if="faq && faq.categories.lenfth != 0">
                                <div v-for="categorie in faq.categories">
                                    <el-collapse accordion>
                                    <span class="categorie-title">{{ categorie.title }}</span>
                                        <el-collapse-item v-for="item in categorie.blocks">
                                            <template slot="title">
                                                <span>{{ item.question }}</span>
                                            </template>
                                            <div>{{ item.answer }}</div>
                                        </el-collapse-item>
                                    </el-collapse>
                                </div>
                            </template>
                            <Alert v-else type="warning">{{ translations.menuHelp }}</Alert>
                        </el-col>

                        <el-col :xs="8" :sm="8" :md="8" :lg="8">
                            <div class="help-content">
                                <div class="documentation">
                                    <p>{{ translations.needHelp }}</p>
                                    <el-button type="primary" @click="openDocumentation()">{{ translations.downloadPDF }}</el-button>
                                </div>
                                <br>
                                <div class="contact-us">
                                    <span class="contact-text">{{ translations.cannotFindAnswer }}</span>
                                    <el-button type="text" @click="redirectToAddonsContact()">{{ translations.contactUs }}</el-button>
                                </div>
                            </div>
                        </el-col>

                    </el-row>

                </PsCardBlock>

            </PsCard>
        </el-col>
    </el-row>
</template>

<script>
import PsCard from '@/components/panel/PsCard.vue'
import PsCardHeader from '@/components/panel/PsCardHeader.vue'
import PsCardBlock from '@/components/panel/PsCardBlock.vue'
import PsCardFooter from '@/components/panel/PsCardFooter.vue'
import Alert from '@/components/Alert.vue'
import { getFaq } from '@/api/ajax.js'
import { mapState } from 'vuex'

export default {
    name: 'help',
    components: {
        PsCard,
        PsCardHeader,
        PsCardBlock,
        PsCardFooter,
        Alert
    },
    data: function () {
        return {
            faq: null,
            baseUrl: baseUrl
        }
    },
    methods: {
        redirectToAddonsContact: function () {
            window.open('http://addons.prestashop.com/contact-form.php', '_blank')
        },
        openDocumentation: function () {
            window.open(documentation, '_blank')
        }
    },
    computed: {
        ...mapState([
            'translations'
        ])
    },
    created: function () {
        getFaq(module_key, ps_version, iso_code).then(response => {
            if (response.categories) {
                this.faq = response
            }
        })
    }
}
</script>

<style scoped lang="scss">
.categorie-title {
    display: block;
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
}
.module-tips {
    font-size: 14px;
    display: flex;
    margin-bottom: 15px;
}
.module-image {
    margin-right: 15px;
}
.module-text ul {
    margin-top: 15px;
}
h3 {
    font-size: 18px !important;
    font-weight: 600 !important;
    line-height: 24px !important;
    margin: 0px 0px 16px !important;
}
.categorie-title {
    color: #363A41;
    font-size: 14px;
    font-weight: 600;
    line-height: 21px;
}
.help-content {
    text-align: center;
    padding: 20px;
}
.help-content .documentation {
    background-color: #F7F7F7;
    padding: 30px;
}
.help-content .documentation p {
    color: #363A41;
    font-size: 14px;
    font-weight: 600;
    line-height: 21px;
}
.contact-us .contact-text {
    font-size: 14px;
}
</style>
