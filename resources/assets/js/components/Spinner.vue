<template>
    <div class="half-circle-spinner" :class="modifierClass">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>
</template>

<script>
export default {
  name: 'Spinner',
  props: ['modifierClass']
}
</script>