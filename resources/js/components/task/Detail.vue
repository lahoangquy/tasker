<template>
  <div v-if="open" class="fixed w-screen h-screen bg-gray-900 bg-opacity-50 top-0 right-0" id="task-detail-modal">
    <div class="flex justify-end">
      <div class="bg-gray-100 w-full md:max-w-5xl h-screen p-6">
        <div class="flex items-center text-red-500 space-x-2 mb-4 cursor-pointer" @click="$emit('updateTaskModal', false)">
          <svg-vue icon="arrow-left" class="w-5 h-5" />
          <span>Return to list</span>
        </div>
        <div class="bg-white h-full rounded-md border border-gray-200 flex flex-col md:flex-row">
          <div class="flex-1 overflow-y-auto">
            <h3 class="border-b border-gray-200 p-6 text-2xl font-medium">
              {{ project.title }}
            </h3>
            <div class="border-b border-gray-200 p-6">
              <TaskPostInfo :project="project" />
              <div class="mt-6 text-gray-700 text-sm" v-html="project.description"></div>
              <task-attachment :project="project" />
            </div>

            <!-- Task activities -->
            <TaskActivities :project="project" />
          </div>

          <div v-if="user.role === 'tasker'" class="flex-shrink-0 border-l border-gray-200 py-6 w-full md:w-2/6">
            <div class="flex justify-center border-b border-gray-200 pb-6">
              <a
                v-show="!isAlreadySubmittedProposal.length"
                :href="`/tasks/proposal/${project.id}`"
                class="bg-red-500 rounded-full px-6 py-2 text-white shadow-md hover:bg-red-600 transition duration-300"
              >
                Submit a proposal
              </a>
            </div>
            <div class="mt-6 p-6">
              <h3 class="text-lg">About Poster</h3>
              <div class="text-sm text-gray-500 space-y-2 mt-2">
                <p class="flex items-start space-x-2">
                  <svg-vue icon="user-circle" class="w-5 h-5" />
                  <span>{{ project.poster.name }}</span>
                </p>
                <Rating />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Rating from '../Rating.vue';
import TaskActivities from './TaskActivities.vue';
import TaskAttachment from './TaskAttachment.vue';
import TaskOfferList from './TaskOfferList.vue';
import TaskPostInfo from './TaskPostInfo.vue';
export default {
  components: {
    TaskAttachment,
    TaskPostInfo,
    TaskOfferList,
    Rating,
    TaskActivities,
    TaskActivities
  },
  props: {
    open: {
      type: Boolean,
      default: false
    },
    project: {
      required: true,
      default: null
    },
    user: {
      type: Object,
      default: null
    }
  },
  computed: {
    isAlreadySubmittedProposal() {
      return this.project.offers.filter((item) => item.user_id == this.user.id && item.status != 2 && item.project_id == this.project.id);
    }
  }
};
</script>
