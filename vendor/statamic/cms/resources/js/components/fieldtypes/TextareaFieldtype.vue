<template>
    <textarea-input
        :focus="config.focus"
        :name="name"
        :isReadOnly="isReadOnly"
        :limit="config.character_limit || null"
        :placeholder="__(config.placeholder)"
        :value="value"
        :id="fieldId"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
        @input="updateDebounced" />
</template>

<script>
import Fieldtype from './Fieldtype.vue';

export default {

    mixins: [Fieldtype]

}
</script>
