<template>
<el-row :gutter="10">
    <el-col :span="22" class="pb-3">
        <el-input resize="none" placeholder="Введите текст..." autosize :disabled="load" type="textarea" v-model="taskname" size="small" />
    </el-col>
    <el-col :span="2">
        <el-button @click="createTask()" :disabled="taskname.length == 0" :loading="load" size="small" type="primary" :icon="Icons.Plus" />
    </el-col>
</el-row>
</template>

<script setup>
import axios from 'axios';
import * as Icons from '@element-plus/icons-vue'
import {
    ref,
    computed
} from 'vue';
import {
    ElMessage
} from 'element-plus';
import Task from './Task.vue';

const taskname = ref("");
const load = ref(false);

const emit = defineEmits(['created']);

const {date} = defineProps({
    date:{
        type: Object,
    }
});
const tasks = defineModel();

function createTask() {
    load.value = true;
    axios.post(route('calendar.task.store'), {
            name: taskname.value,
            date: date.toISOString()
        })
        .then((d) => {
            // console.log(d);
            ElMessage.success("Задача добавлена");
            emit('created', d.data);
            taskname.value = "";
            // tasks.push(d.data);
        })
        .catch(() => {
            ElMessage.error("Не удалось добавить задачу");
        })
        .finally(() => {
            load.value = false;
        })
}
</script>
