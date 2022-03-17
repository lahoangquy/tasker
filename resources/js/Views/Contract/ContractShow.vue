<template>
  <view-dashboard-layout>
    <white-box>
      <div v-if="!loading">
        <white-box-heading class="p-6 flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex-1 text-center md:text-left">
            <h3 class="text-xl font-medium mb-4 truncate max-w-[600px]">{{ contract.project.title }}</h3>
            <div class="text-sm text-gray-600">
              <span v-if="contract.status == 2" class="flex items-center gap-2">
                <svg-vue icon="check-circle-solid" class="w-5 h-5 text-orange-600" />
                <span>Completed {{ formatDate(contract.completed_at) }}</span>
              </span>
              <span v-if="contract.status == 1">In-Process {{ contract.started_at }}</span>
            </div>
          </div>
          <button
            v-if="user.role === 'tasker'"
            :disabled="contract.project.hasRequestCompleted"
            @click="sendRequestCompleted(contract.project)"
            class="bg-blue-600 text-white text-md rounded-full px-6 py-2"
          >
            Send Request Completed
          </button>
          <button
            v-if="user.role === 'poster' && contract.project.hasRequestCompleted"
            @click="AcceptRequestCompleted(contract.project)"
            class="bg-blue-600 text-white text-md rounded-full px-6 py-2"
          >
            Accept Request Completed
          </button>
        </white-box-heading>
        <white-box-content class="px-6 py-8">
          <tab-wrapper :tabs="tabs" :selectedTab="selectedTab" @select-tab="selectTab" />

          <div class="tab-content">
            <div v-show="selectedTab === 'milestone-earning'">
              <h3>Milestone</h3>
            </div>
            <!-- Review tab -->
            <div v-show="selectedTab === 'messages-files'">
              <messages-and-files :project="contract.project" :student="contract.student" :owner="contract.owner" />
            </div>
            <div v-show="selectedTab === 'feedbacks'">
              <h3>Feedbacks</h3>
            </div>
          </div>
        </white-box-content>
      </div>
      <div v-else><p class="text-center py-20">Loading</p></div>
    </white-box>
  </view-dashboard-layout>
</template>

<script>
import WhiteBox from '../../components/shared/WhiteBox.vue';
import WhiteBoxContent from '../../components/shared/WhiteBoxContent.vue';
import WhiteBoxHeading from '../../components/shared/WhiteBoxHeading.vue';
import ViewDashboardLayout from '../ViewDashboardLayout.vue';
import TabWrapper from '../../components/shared/TabWrapper.vue';
import MessagesAndFiles from './MessagesAndFiles.vue';

export default {
  components: { ViewDashboardLayout, WhiteBox, WhiteBoxHeading, WhiteBoxContent, TabWrapper, MessagesAndFiles },
  data() {
    return {
      loading: false,
      contract: {},
      selectedTab: 'milestone-earning',
      tabs: [
        { name: 'Milestone', target: 'milestone-earning' },
        { name: 'Messages & Files', target: 'messages-files' },
        { name: 'Feedbacks', target: 'feedbacks' }
      ]
    };
  },
  props: {
    contractId: Number
  },
  computed: {},
  methods: {
    selectTab(tab) {
      this.selectedTab = tab;
    },
    getContract() {
      this.loading = true;
      this.$util.get(`contracts/${this.contractId}`).then((res) => {
        this.contract = res.data;
        this.loading = false;
      });
    },
    async sendRequestCompleted(project) {
      let loader = this.$loading.show({
        container: this.fullPage ? null : this.$refs.formContainer,
        canCancel: true,
        onCancel: this.onCancel
      });

      try {
        await this.$util.post(`projects/request-completed`, { project_id: project.id });
        this.contract.project.hasRequestCompleted = true;
        Toast.fire('Your request was sent', '', 'success');
      } catch (error) {
        if (error.response) {
          Toast.fire('Something went wrong. Please try again', '', 'error');
        }
      } finally {
        loader.hide();
      }
    }
  },
  created() {
    this.getContract();

    const hash = window.location.hash.substring(1);
    this.selectedTab = hash !== '' ? hash : 'milestone-earning';
  }
};
</script>

<style></style>
