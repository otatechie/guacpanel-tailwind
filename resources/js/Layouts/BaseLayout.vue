<template>

    <FlashMessage></FlashMessage>

    <header class="sticky inset-0 z-50 border-b border-slate-100 bg-white">
        <div class="mx-auto max-w-7xl">
            <div class="px-6  lg:max-w-2xl lg:pl-8 lg:pr-0">
                <nav class="flex items-center justify-between lg:justify-start" aria-label="Global">
                    <Link :href="route('public.welcome')" class="-m-1.5 p-1.5">
                    <span class="sr-only">Afropulse logo</span>
                    <img alt="Afropulse logo" :class="[
                        'h-12 w-auto',
                        $page.url === '/welcome' ? 'brightness-0 invert' : ''
                    ]" src="/images/afropulse-logo.svg">
                    </Link>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700 lg:hidden" @click="open = !open">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="hidden lg:ml-12 lg:flex lg:gap-x-10">
                        <Link :href="route('public.welcome')" class="uppercase font-semibold leading-6 text-black">
                        Browse
                        beats</Link>
                        <a href="#" class="uppercase font-semibold leading-6 text-black">
                            Producers</a>
                        <Link :href="route('login')" class="uppercase font-semibold leading-6 text-black">Log in</Link>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Mobile menu, show/hide based on menu open state. -->
        <div v-if="open" class="lg:hidden" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-50"></div>
            <div
                class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">Your Company</span>
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
                            alt="">
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="open = false">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-0.5 py-3">
                            <Link :href="route('public.discover')"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-gray-50">
                            Get involved</Link>
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-gray-50">Start
                                a campaign</a>
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-gray-50">Log
                                in</a>
                        </div>
                        <div class="py-3">
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-white hover:bg-gray-50">Donate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <slot></slot>

</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import Logo from '../Shared/Logo.vue'
import FlashMessage from '../Shared/FlashMessage.vue';
const page = usePage()
const open = ref(false)

</script>
