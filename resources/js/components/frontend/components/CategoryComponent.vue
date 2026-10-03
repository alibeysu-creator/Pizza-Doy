<template>
    <Swiper dir="ltr" :speed="1000" slidesPerView="auto" :spaceBetween="16" class="menu-slides" @swiper="onSwiper">
        <SwiperSlide v-for="category in categories" :key="category.id" :id="'cat-' + category.slug" class="!w-fit">
            <router-link v-if="design === categoryDesignEnum.FIRST" @click="scrollToCategory(category.slug)"
                :to="{ name: 'frontend.menu', query: { s: category.slug } }"
                :class="checkIsActive(category.slug) ? 'menu-category-active' : ''"
                class="group swiper-slide w-fit text-center">
                <div class="group-hover:bg-white group-hover:shadow-[0px_4px_16px_#7E858E29] flex items-center justify-center w-[120px] h-[120px] rounded-full bg-[#F7F7FC] mb-5 transition-all duration-300">
                    <img class="h-12 drop-shadow-category" :src="category.thumb" alt="category">
                </div>
                <h3 class="text-xs text-center font-medium max-w-[100px] w-full line-clamp-2">{{ category.name }}</h3>
            </router-link>

            <router-link :class="checkIsActive(category.slug) ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100'" @click="scrollToCategory(category.slug)" v-else-if="design === categoryDesignEnum.SECOND" :to="{ name: 'frontend.menu', query: { s: category.slug } }" class="!w-fit flex items-center justify-center px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200">
                {{ category.name }}
            </router-link>
        </SwiperSlide>
    </Swiper>
</template>

<script>

import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';

export default {
    name: "CategoryComponent",
    props: {
        categories: Object,
        design: Number,
        activeSlug: String
    },
    components: {
        Swiper,
        SwiperSlide,
    },
    data() {
        return {
            categoryDesignEnum: categoryDesignEnum,
            swiperInstance: null
        }
    },
    methods: {
        onSwiper(swiper) {
            this.swiperInstance = swiper;
        },
        checkIsActive(slug) {
            return this.activeSlug === slug;
        },
        scrollToCategory(slug) {
            if (this.$route.query.s === slug) {
                const element = document.getElementById(slug);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }
    },
    watch: {
        activeSlug: function (newSlug) {
            if (newSlug && this.swiperInstance) {
                const index = this.categories.findIndex(c => c.slug === newSlug);
                if (index !== -1) {
                    this.swiperInstance.slideTo(index);
                }
            }
        }
    }
}
</script>
