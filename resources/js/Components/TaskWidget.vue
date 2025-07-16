<template>
<el-card body-style="padding: 10px">
    <template #header>
        <el-row :gutter="10">
            <el-col :span="3">
                <el-image style="width: 50px" :src="`/img/tasks.gif`" fit="contain">
                    <template #placeholder>
                        <el-skeleton animated>
                            <template #template>
                                <el-skeleton-item variant="image" style="width: 100%; height: 50px" />
                            </template>
                        </el-skeleton>
                    </template>
                    <template #error>
                        <el-skeleton>
                            <template #template>
                                <el-skeleton-item variant="image" style="width: 100%; height: 50px" />
                            </template>
                        </el-skeleton>
                    </template>
                </el-image>
            </el-col>
            <el-col :span="21">
            <div class="flex flex-row justify-between">
                <h1>Мои задачи</h1>
                <Link :href="route('calendar.tasks')">
                <el-button size="small">Перейти</el-button>
                </Link>
                </div>
                <template v-if="tasks.length > 0">
                    <el-progress class="mt-1" :percentage="completeProgress" status="success" :show-text="false" />
                    <el-text v-if="completeProgress == 100">Вы выполнили все задачи на сегодня!</el-text>
                    <el-text v-else>Выполнено {{Math.round(completeProgress)}}%</el-text>
                </template>
                <el-text v-else>Расскажите о своих планах на сегодня</el-text>
            </el-col>
        </el-row>
    </template>
    <CreateTask :date="date" @created="(ta) => tasks.push(ta)" />
    <div class="flex flex-col gap-2">
        <Task v-for="(t, ti) in tasks" v-model="tasks[ti]" />
    </div>
</el-card>
</template>


<script setup>
import {
    Link
} from "@inertiajs/vue3";
import * as Icons from '@element-plus/icons-vue'
import {computed} from 'vue';
import Task from './Task.vue';
import CreateTask from './CreateTask.vue';


const completeProgress = computed(() => {
    return (100 / tasks.filter((t) => !t.deleted_at).length) * tasks.filter((t) => !t.deleted_at && t.finished_at).length;
});

const {
    tasks, date
} = defineProps({
    tasks: {
        type: Array,
    },
    date: {
        type: Object,
    }
})
</script>
