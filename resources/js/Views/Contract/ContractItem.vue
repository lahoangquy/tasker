<template>
  <div class="p-6 hover:bg-gray-50 transition duration-300">
    <a class="text-lg font-medium inline-block mb-4 hover:text-red-500 hover:underline" :href="`/dashboard/contract/${contract.id}`">{{
      contract.project.title
    }}</a>
    <div class="flex justify-between items-center text-gray-700">
      <span v-if="user.role === 'poster'">Hired: {{ contract.student.name }}</span>
      <span v-else>Hired By: {{ contract.owner.name }}</span>
      <div v-if="contract.status == 1">In-Process</div>
      <div v-else class="flex items-center gap-3">
        <span>Completed</span>
        <span><rating :stars="5" /></span>
      </div>
      <div>
        <span v-if="contract.status == 1">{{ contract.started_at }}</span>
        <span v-else>{{ contract.started_at }} - {{ contract.completed_at }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Rating from '../../components/Rating.vue';
export default {
  components: { Rating },
  props: {
    contract: {
      type: Object,
      default: () => {}
    }
  },
  computed: {
  }
};
</script>

<style></style>
