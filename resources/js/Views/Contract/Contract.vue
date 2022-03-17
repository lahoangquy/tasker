<template>
  <view-dashboard-layout>
    <h1 class="dashboard__heading">All Contracts</h1>
    <white-box>
      <white-box-heading class="flex justify-between px-6 py-4">
        <div class="flex gap-3">
          <select name="" id="" class="form-control">
            <option value="">Status</option>
          </select>
          <select name="" id="" class="form-control">
            <option value="">Start Date</option>
          </select>
        </div>
        <button class="flex-none text-right flex gap-1 items-center">
          <svg-vue icon="download" class="w-5 h-5" />
          <span>Download CSV</span>
        </button>
      </white-box-heading>
      <white-box-content>
        <div class="divide-y divide-gray-200">
          <contract-item v-for="contract in contracts" :key="contract.id" :contract="contract" />
        </div>
      </white-box-content>
    </white-box>
  </view-dashboard-layout>
</template>

<script>
import WhiteBox from '../../components/shared/WhiteBox.vue';
import WhiteBoxContent from '../../components/shared/WhiteBoxContent.vue';
import WhiteBoxHeading from '../../components/shared/WhiteBoxHeading.vue';
import ViewDashboardLayout from '../ViewDashboardLayout.vue';
import ContractItem from './ContractItem.vue';
export default {
  components: { ViewDashboardLayout, WhiteBox, WhiteBoxHeading, WhiteBoxContent, ContractItem },
  data() {
    return {
      contracts: []
    };
  },
  methods: {
    async getContracts() {
      const { data } = await this.$util.get(`contracts`);
      this.contracts = data;
    }
  },
  created() {
    this.getContracts();
  }
};
</script>

<style></style>
