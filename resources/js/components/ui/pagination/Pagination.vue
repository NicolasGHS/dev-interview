<script setup lang="ts">
import { ChevronLeft, ChevronRight, MoreHorizontal } from 'lucide-vue-next'
import { computed } from 'vue'
import { Button } from '@/components/ui/button'

export interface PaginationProps {
  currentPage: number
  totalPages: number
  onPageChange: (page: number) => void
  showPageNumbers?: boolean
  maxPageNumbers?: number
}

const props = withDefaults(defineProps<PaginationProps>(), {
  showPageNumbers: true,
  maxPageNumbers: 7,
})

const emit = defineEmits<{
  pageChange: [page: number]
}>()

const pages = computed(() => {
  const { currentPage, totalPages, maxPageNumbers } = props
  const pages: Array<{ type: 'page' | 'ellipsis'; value?: number }> = []
  
  if (totalPages <= maxPageNumbers) {
    // Show all pages
    for (let i = 1; i <= totalPages; i++) {
      pages.push({ type: 'page', value: i })
    }
  } else {
    // Show first page
    pages.push({ type: 'page', value: 1 })
    
    // Add ellipsis and pages around current page
    if (currentPage > 3) {
      pages.push({ type: 'ellipsis' })
    }
    
    const start = Math.max(2, currentPage - 1)
    const end = Math.min(totalPages - 1, currentPage + 1)
    
    for (let i = start; i <= end; i++) {
      if (i !== 1 && i !== totalPages) {
        pages.push({ type: 'page', value: i })
      }
    }
    
    if (currentPage < totalPages - 2) {
      pages.push({ type: 'ellipsis' })
    }
    
    // Show last page
    if (totalPages > 1) {
      pages.push({ type: 'page', value: totalPages })
    }
  }
  
  return pages
})

const handlePageChange = (page: number) => {
  if (page !== props.currentPage && page >= 1 && page <= props.totalPages) {
    emit('pageChange', page)
  }
}

const goToFirst = () => handlePageChange(1)
const goToPrevious = () => handlePageChange(props.currentPage - 1)
const goToNext = () => handlePageChange(props.currentPage + 1)
const goToLast = () => handlePageChange(props.totalPages)

const canGoToPrevious = computed(() => props.currentPage > 1)
const canGoToNext = computed(() => props.currentPage < props.totalPages)
</script>

<template>
  <nav class="mx-auto flex w-full justify-center" role="navigation" aria-label="pagination">
    <ul class="flex flex-row items-center gap-1">
      <!-- First page button -->
      <li>
        <Button
          variant="outline"
          size="icon"
          :disabled="!canGoToPrevious"
          @click="goToFirst"
          aria-label="Go to first page"
        >
          <ChevronLeft class="h-4 w-4" />
          <ChevronLeft class="h-4 w-4" />
        </Button>
      </li>
      
      <!-- Previous page button -->
      <li>
        <Button
          variant="outline"
          size="icon"
          :disabled="!canGoToPrevious"
          @click="goToPrevious"
          aria-label="Go to previous page"
        >
          <ChevronLeft class="h-4 w-4" />
        </Button>
      </li>

      <!-- Page numbers -->
      <template v-if="showPageNumbers">
        <template v-for="(page, index) in pages" :key="`page-${index}`">
          <li v-if="page.type === 'page'">
            <Button
              :variant="currentPage === page.value ? 'default' : 'outline'"
              size="icon"
              @click="handlePageChange(page.value!)"
              :aria-label="`Go to page ${page.value}`"
              :aria-current="currentPage === page.value ? 'page' : undefined"
            >
              {{ page.value }}
            </Button>
          </li>
          <li v-else>
            <div class="flex h-9 w-9 items-center justify-center">
              <MoreHorizontal class="h-4 w-4" />
              <span class="sr-only">More pages</span>
            </div>
          </li>
        </template>
      </template>

      <!-- Next page button -->
      <li>
        <Button
          variant="outline"
          size="icon"
          :disabled="!canGoToNext"
          @click="goToNext"
          aria-label="Go to next page"
        >
          <ChevronRight class="h-4 w-4" />
        </Button>
      </li>
      
      <!-- Last page button -->
      <li>
        <Button
          variant="outline"
          size="icon"
          :disabled="!canGoToNext"
          @click="goToLast"
          aria-label="Go to last page"
        >
          <ChevronRight class="h-4 w-4" />
          <ChevronRight class="h-4 w-4" />
        </Button>
      </li>
    </ul>
  </nav>
</template>