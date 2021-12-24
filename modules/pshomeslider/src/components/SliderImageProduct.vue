<template>
    <div class="slider">
        <el-button v-if="images.length > perPage && this.currentPage !== 1" @click="prev()" class="prev" style="float: left;" type="text"><i class="material-icons">arrow_back_ios</i></el-button>
        <el-container id="slider" class="flex-wrap" :gutter="20">
            <div v-for="image in filteredImages" :key="image.id_image" class="item" :class="{selected: selectedImage === image.id_image}">
                <img :src="image.image_link" width="75" @click="selectImage(image.id_image)" height="75">
            </div>
        </el-container>
        <el-button v-if="images.length > perPage && this.currentPage !== this.totalPage" @click="next()" class="next" style="float: right;" type="text"><i class="material-icons">arrow_forward_ios</i></el-button>
    </div>
</template>

<script>
export default {
    name: 'SliderImageProduct',
    data () {
        return {
            currentPage: 1,
            perPage: 3
        }
    },
    props: {
        images: {
            type: Array,
            require: true
        },
        selected: {
            type: String,
            require: true
        }
    },
    computed: {
        selectedImage: function () {
            return this.selected
        },
        filteredImages: function () {
            const offset = (this.currentPage - 1) * this.perPage
            return this.images.slice(offset, offset + this.perPage)
        },
        totalPage: function () {
            return Math.ceil(this.images.length / this.perPage)
        }
    },
    methods: {
        selectImage: function (idImage) {
            this.$emit('update:selectedImage', idImage)
        },
        prev: function () {
            if (this.currentPage !== 1) {
                this.currentPage -= 1
            }
        },
        next: function () {
            if (this.currentPage !== this.totalPage) {
                this.currentPage += 1
            }
        }
    }
}
</script>

<style scoped lang="scss">
.flex-wrap {
    margin-left: 20px;
    margin-right: 20px;
}
.item {
    flex: 0 0 auto;
    width: 75px;
    height: 75px;
    cursor: pointer;
    margin: 6px;
}

.item > img {
  border:2px solid transparent;
}

.selected > img {
  border: 3px solid #2eacce;
}

.slider {
    position: relative;
    width: 265px;
}

.prev {
    position: absolute;
    top: 26px;
    left: -10px;
}

.next {
    position: absolute;
    top: 26px;
    right: -52px;
}
</style>
