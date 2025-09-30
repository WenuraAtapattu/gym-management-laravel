<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Manage Reviews') }}
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-6">
              <div class="flex space-x-4">
                <button
                  @click="activeTab = 'pending'"
                  :class="{
                    'bg-indigo-100 text-indigo-700': activeTab === 'pending',
                    'text-gray-600 hover:text-gray-800': activeTab !== 'pending',
                  }"
                  class="px-4 py-2 text-sm font-medium rounded-md"
                >
                  {{ __('Pending Reviews') }}
                  <span
                    v-if="pendingStats.pending_reviews > 0"
                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                  >
                    {{ pendingStats.pending_reviews }}
                  </span>
                </button>
                <button
                  @click="activeTab = 'approved'"
                  :class="{
                    'bg-indigo-100 text-indigo-700': activeTab === 'approved',
                    'text-gray-600 hover:text-gray-800': activeTab !== 'approved',
                  }"
                  class="px-4 py-2 text-sm font-medium rounded-md"
                >
                  {{ __('Approved Reviews') }}
                  <span
                    v-if="pendingStats.approved_reviews > 0"
                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                  >
                    {{ pendingStats.approved_reviews }}
                  </span>
                </button>
              </div>
              
              <div class="flex items-center space-x-2">
                <div class="text-sm text-gray-600">
                  {{ __('Total:') }} {{ pendingStats.total_reviews }}
                </div>
              </div>
            </div>

            <div v-if="loading" class="flex justify-center py-12">
              <LoadingSpinner />
            </div>

            <div v-else>
              <div v-if="reviews.length === 0" class="text-center py-12">
                <p class="text-gray-500">
                  {{ activeTab === 'pending' 
                    ? __('No pending reviews found.') 
                    : __('No approved reviews found.') }}
                </p>
              </div>

              <div v-else class="space-y-6">
                <div 
                  v-for="review in reviews" 
                  :key="review.id"
                  class="p-4 border rounded-lg"
                  :class="{
                    'bg-yellow-50 border-yellow-200': activeTab === 'pending',
                    'bg-white border-gray-200': activeTab === 'approved'
                  }"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center">
                        <div class="flex items-center">
                          <StarRating 
                            :rating="review.rating" 
                            :interactive="false"
                            size="sm"
                          />
                        </div>
                        <h3 class="ml-2 text-lg font-medium text-gray-900">
                          {{ review.title }}
                        </h3>
                      </div>
                      
                      <div class="mt-1 text-sm text-gray-600">
                        <p>{{ review.comment }}</p>
                      </div>
                      
                      <div class="mt-2 text-xs text-gray-500">
                        <span v-if="review.user">
                          {{ __('By') }} {{ review.user.name }}
                          <span v-if="review.user.is_guest">({{ __('Guest') }})</span>
                        </span>
                        <span v-else>
                          {{ __('By Guest') }}
                        </span>
                        <span class="mx-1">•</span>
                        <span>{{ formatDate(review.created_at) }}</span>
                        <span v-if="review.product" class="mx-1">•</span>
                        <a 
                          v-if="review.product" 
                          :href="route('products.show', review.product.slug)" 
                          class="text-indigo-600 hover:text-indigo-800 hover:underline"
                          target="_blank"
                        >
                          {{ review.product.name }}
                        </a>
                      </div>
                    </div>
                    
                    <div class="flex items-center ml-4 space-x-2">
                      <button
                        v-if="activeTab === 'pending'"
                        @click="approveReview(review)"
                        class="p-2 text-green-600 rounded-full hover:bg-green-100"
                        :title="__('Approve')"
                        :disabled="processing"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>
                      
                      <button
                        @click="confirmReject(review)"
                        class="p-2 text-red-600 rounded-full hover:bg-red-100"
                        :title="__('Reject')"
                        :disabled="processing"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <Pagination 
                  v-if="reviews.meta.last_page > 1"
                  :pagination="reviews.meta"
                  @pagination-change-page="getResults"
                  class="mt-6"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Reject Confirmation Modal -->
    <ConfirmationModal :show="showRejectModal" @close="closeModal">
      <template #title>
        {{ __('Reject Review') }}
      </template>
      
      <template #content>
        <p class="text-gray-600">
          {{ __('Are you sure you want to reject this review? This action cannot be undone.') }}
        </p>
      </template>
      
      <template #footer>
        <SecondaryButton @click="closeModal">
          {{ __('Cancel') }}
        </SecondaryButton>
        
        <DangerButton 
          class="ml-3" 
          @click="rejectReview"
          :disabled="processing"
        >
          <span v-if="processing" class="flex items-center">
            <svg class="w-4 h-4 mr-2 -ml-1 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('Processing...') }}
          </span>
          <span v-else>
            {{ __('Reject Review') }}
          </span>
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AdminLayout>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import StarRating from '@/Components/StarRating.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

export default defineComponent({
  components: {
    AdminLayout,
    LoadingSpinner,
    StarRating,
    Pagination,
    ConfirmationModal,
    SecondaryButton,
    DangerButton,
  },
  
  props: {
    pendingReviews: {
      type: Object,
      default: () => ({
        data: [],
        meta: {},
      }),
    },
    approvedReviews: {
      type: Object,
      default: () => ({
        data: [],
        meta: {},
      }),
    },
    stats: {
      type: Object,
      default: () => ({
        total_reviews: 0,
        pending_reviews: 0,
        approved_reviews: 0,
      }),
    },
  },
  
  setup(props) {
    const activeTab = ref('pending');
    const loading = ref(false);
    const processing = ref(false);
    const showRejectModal = ref(false);
    const selectedReview = ref(null);
    const reviews = ref({ data: [], meta: {} });
    const pendingStats = ref({
      total_reviews: props.stats.total_reviews || 0,
      pending_reviews: props.stats.pending_reviews || 0,
      approved_reviews: props.stats.approved_reviews || 0,
    });

    // Initialize with pending reviews
    onMounted(() => {
      reviews.value = props.pendingReviews;
    });

    const getResults = async (page = 1) => {
      loading.value = true;
      try {
        const url = activeTab.value === 'pending' 
          ? route('admin.reviews.pending', { page })
          : route('admin.reviews.approved', { page });
        
        const response = await axios.get(url);
        reviews.value = response.data;
      } catch (error) {
        console.error('Error fetching reviews:', error);
      } finally {
        loading.value = false;
      }
    };

    const approveReview = async (review) => {
      if (processing.value) return;
      
      processing.value = true;
      try {
        await axios.post(route('admin.reviews.approve', review.id));
        
        // Update UI
        reviews.value.data = reviews.value.data.filter(r => r.id !== review.id);
        pendingStats.value.pending_reviews--;
        pendingStats.value.approved_reviews++;
        
        // Show success message
        window.toast.success(__('Review approved successfully.'));
      } catch (error) {
        console.error('Error approving review:', error);
        window.toast.error(__('Failed to approve review. Please try again.'));
      } finally {
        processing.value = false;
      }
    };

    const confirmReject = (review) => {
      selectedReview.value = review;
      showRejectModal.value = true;
    };

    const closeModal = () => {
      showRejectModal.value = false;
      selectedReview.value = null;
    };

    const rejectReview = async () => {
      if (!selectedReview.value || processing.value) return;
      
      processing.value = true;
      try {
        await axios.delete(route('admin.reviews.reject', selectedReview.value.id));
        
        // Update UI
        reviews.value.data = reviews.value.data.filter(r => r.id !== selectedReview.value.id);
        
        if (activeTab.value === 'pending') {
          pendingStats.value.pending_reviews--;
        } else {
          pendingStats.value.approved_reviews--;
        }
        
        pendingStats.value.total_reviews--;
        
        // Show success message
        window.toast.success(__('Review rejected successfully.'));
      } catch (error) {
        console.error('Error rejecting review:', error);
        window.toast.error(__('Failed to reject review. Please try again.'));
      } finally {
        processing.value = false;
        closeModal();
      }
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString();
    };

    // Watch for tab changes
    watch(activeTab, (newTab) => {
      if (newTab === 'pending') {
        reviews.value = props.pendingReviews;
      } else {
        reviews.value = props.approvedReviews;
      }
    });

    return {
      activeTab,
      loading,
      processing,
      reviews,
      pendingStats,
      showRejectModal,
      getResults,
      approveReview,
      confirmReject,
      closeModal,
      rejectReview,
      formatDate,
    };
  },
});
</script>
