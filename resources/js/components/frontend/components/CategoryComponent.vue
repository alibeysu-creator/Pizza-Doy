<template>
    <Swiper dir="ltr" :speed="1000" slidesPerView="auto" :spaceBetween="16" class="menu-slides border-b-4 border-primary-light" @swiper="onSwiper">
        <SwiperSlide v-for="category in categories" :key="category.id" :id="'cat-' + category.slug" class="!w-fit">
            <router-link v-if="design === categoryDesignEnum.FIRST" @click="scrollToCategory(category.slug)"
                :to="{ name: 'frontend.menu', query: { s: category.slug } }"
                :class="checkIsActive(category.slug) ? 'menu-category-active' : ''"
                class="!w-fit flex items-center text-center gap-4 p-3 border-b-2 border-transparent transition hover:!rounded-2xl hover:bg-[#FFEDF4]">
                <img class="h-12 drop-shadow-category" :src="category.thumb" alt="category">
                <h3 class="text-xs font-medium max-w-[100px] w-full text-start line-clamp-2">{{ category.name }}</h3>
            </router-link>

            <router-link :class="checkIsActive(category.slug) ? 'menu-category-active border-primary' : ''"
                @click="scrollToCategory(category.slug)" v-else-if="design === categoryDesignEnum.SECOND"
                :to="{ name: 'frontend.menu', query: { s: category.slug } }"
                class="!w-fit flex items-center text-center gap-4 p-3 rounded-2xl border-b-2 border-transparent transition hover:bg-[#FFEDF4]">
                <img class="h-9 drop-shadow-category" :src="category.thumb" alt="category">
                <h3 class="text-xs font-medium max-w-[100px] w-full text-start line-clamp-2">{{ category.name }}</h3>
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
