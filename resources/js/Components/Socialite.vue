<script setup>

import { computed } from 'vue'
import GoogleIcon from "./Icons/GoogleIcon.vue";
import FacebookIcon from "./Icons/FacebookIcon.vue";
import GitHubIcon from "./Icons/GitHubIcon.vue";
import LinkedInIcon from "./Icons/LinkedInIcon.vue";

const props = defineProps({
  providersConfig: {
    type: Object,
    required: false,
    default: () => ({
      button_text: '',
      providers: [],
    }),
  },
  iconsOnly: {
    type: Boolean,
    default: false,
  },
})

const redirect = (provider) => {
  window.location.href = route("social.redirect", { provider });
};

const gridClass = computed(() => {
  const providers = Object.keys(props.providersConfig.providers).length;
  const onlyIcons = props.iconsOnly;
  if (providers == 1) return 'grid-cols-1'
  if (onlyIcons) {
    if (providers % 4 == 0) return 'grid-cols-4'
    if (providers % 2 == 0) return 'grid-cols-2'
    return 'grid-cols-3';
  }
  return 'grid-cols-2';
})

const btnWrapperClass = (i) => {
  if (props.iconsOnly) return ''
  const providers = Object.keys(props.providersConfig.providers).length;
  if (providers % 2 == 0) {
    return '';
  }
  if (i + 1 == providers) {
    return 'col-span-2';
  }
  return '';
};

const providerIcon = (provider) => {
  if (provider === 'google') {
    return GoogleIcon
  }
  if (provider === 'facebook') {
    return FacebookIcon
  }
  if (provider === 'github') {
    return GitHubIcon
  }
  if (provider === 'linkedin') {
    return LinkedInIcon
  }

  return null
}

const providerClasses = (provider) => {
  // if (provider == 'google') {
  //   return 'bg-red-500 hover:bg-red-600 hover:text-white text-white dark:bg-red-600 dark:hover:bg-red-700';
  // }
  // if (provider == 'facebook') {
  //   return 'bg-blue-600 hover:bg-blue-700 hover:text-white text-white dark:bg-blue-700 dark:hover:bg-blue-800';
  // }
  // if (provider == 'github') {
  //   return 'bg-gray-900 hover:bg-gray-700 hover:text-white text-white dark:border-white dark:bg-transparent dark:hover:bg-gray-900';
  // }
  // if (provider == 'linkedin') {
  //   return 'bg-blue-700 hover:bg-blue-800 hover:text-white text-white dark:bg-blue-800 dark:hover:bg-blue-900';
  // }
  return null;
};

</script>

<template>
  <div
    class="grid gap-4"
    :class="gridClass"
  >    <template
      v-for="(provider, index, i) in providersConfig.providers"
      :key="index"
    >
      <span :class="btnWrapperClass(i)">
        <button
          type="button"
          variant="outline"
          :class="providerClasses(index)"
          :tabIndex="5"
          @click="redirect(index)"
          class="flex items-center justify-center cursor-pointer transition-colors text-sm gap-2 p-2.5 btn-secondary w-full"
        >
          <component
            :is="providerIcon(index)"
            class="size-5.5"
          />
          <span v-if="!iconsOnly">
            {{ providersConfig.button_text.replace("{provider}", index) }}
          </span>
        </button>
      </span>
    </template>
  </div>
</template>
