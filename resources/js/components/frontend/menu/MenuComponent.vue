<template>
    <LoadingComponent :props="loading" />
    <section class="mb-16 mt-8">
        <div class="container">
            <div v-if="categories.length > 0" class="mb-12 sticky top-[118px] lg:top-[74px] z-[10] bg-white pt-4 pb-2">
                <CategoryComponent :categories="categories" :design="categoryProps.design" :activeSlug="activeSlug" />
            </div>

            <div class="menu-sections ">
                <div v-for="category in categories" :key="category.id" :id="category.slug" class="menu-section mb-12 scroll-mt-[210px] lg:scroll-mt-[170px]">
                    <div v-if="category.items && category.items.length > 0">
                        <div class="flex gap-4 flex-col sm:flex-row items-center justify-between mb-6">
                            <h2 class="capitalize text-[26px] leading-[40px] font-semibold text-center sm:text-left text-primary">
                                {{ category.name }}
                            </h2>
                            <div class="flex items-center gap-3">
                                <button type="button" class="lab lab-row-vertical lab-font-size-20 text-xl"
                                    v-on:click="itemProps.design = itemDesignEnum.LIST"
                                    :class="itemProps.design === itemDesignEnum.LIST ? 'text-primary' : 'text-[#A0A3BD]'"></button>
                                <button type="button" class="lab lab-element-3 lab-font-size-20 text-xl"
                                    v-on:click="itemProps.design = itemDesignEnum.GRID"
                                    :class="itemProps.design === itemDesignEnum.GRID ? 'text-primary' : 'text-[#A0A3BD]'"></button>
                            </div>
                        </div>
                        <ItemComponent :items="category.items" :design="itemProps.design" />
                    </div>
                </div>
            </div>

            <div v-if="categories.length === 0" class="mt-12">
                <div class="max-w-[250px] mx-auto">
                    <img class="w-full mb-8" :src="setting.item_not_found" alt="image_order_not_found">
                </div>
                <span class="w-full mb-4 text-center text-black">{{ $t('message.no_items_found') }}</span>
            </div>
        </div>
    </section>
</template>

<script>

import statusEnum from "../../../enums/modules/statusEnum";
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import CategoryComponent from "../components/CategoryComponent";
import ItemComponent from "../components/ItemComponent";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "MenuComponent",
    components: { CategoryComponent, ItemComponent, LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            itemDesignEnum: itemDesignEnum,
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: 'sort',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                },
                design: categoryDesignEnum.SECOND
            },
            itemProps: {
                design: itemDesignEnum.LIST,
            },
            activeSlug: "",
            isScrolling: false
        }
    },
    computed: {
        categories: function () {
            return this.$store.getters['frontendItemCategory/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendItemCategory/lists", this.categoryProps.search).then((res) => {
            this.loading.isActive = false;
            this.$nextTick(() => {
                this.scrollToSection();
                window.addEventListener('scroll', this.handleScroll);
            });
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    unmounted() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        scrollToSection: function () {
            this.$nextTick(() => {
                if (typeof this.$route.query.s !== "undefined" && this.$route.query.s !== "") {
                    const element = document.getElementById(this.$route.query.s);
                    if (element) {
                        this.isScrolling = true;
                        this.activeSlug = this.$route.query.s;
                        
                        // Use a small delay to ensure it scrolls AFTER any potential router default jumps
                        setTimeout(() => {
                            element.scrollIntoView({ behavior: 'smooth' });
                        }, 50);

                        setTimeout(() => {
                            this.isScrolling = false;
                        }, 1200);
                    }
                }
            });
        },
        handleScroll: function () {
            if (this.isScrolling) return;

            const sections = document.querySelectorAll(".menu-section");
            let current = "";

            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= sectionTop - 220) { // Offset buffert for sticky area
                    current = section.getAttribute("id");
                }
            });

            if (current !== "" && this.activeSlug !== current) {
                this.activeSlug = current;
            }
        }
    },
    watch: {
        '$route.query.s': function () {
            this.scrollToSection();
        }
    }
}
</script>