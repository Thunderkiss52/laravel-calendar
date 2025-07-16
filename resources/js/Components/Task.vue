<template>
<div :class="{
    wait: !t.finished_at && !t.deleted_at,
    success: t.finished_at,
    destroy: t.deleted_at
}" class="calendar-task">
    <div class="p-2 py-1 flex justify-between flex-col">
        <div>
            {{t.name}}
        </div>
        <div style="width: 100%;" class="flex justify-between">
            <el-text size="small">{{new Date(t.created_at).toLocaleString()}}</el-text>
            <div class="flex gap-3">
                <el-text v-if="t.deleted_at" size="small">Удалена: {{new Date(t.deleted_at).toLocaleString()}}</el-text>
                <el-text v-else-if="t.finished_at" size="small">Выполнено: {{new Date(t.finished_at).toLocaleString()}}</el-text>
                <template v-else>
                    <el-link :disabled="t.load" :underline="false" size="small" type="primary" @click="finishTask(t)" v-if="!t.finished_at && !t.deleted_at">Выполнить</el-link>
                    <el-link :disabled="t.load" :underline="false" size="small" type="danger" @click="deleteTask(t)" v-if="!t.finished_at && !t.deleted_at" style="margin: 0px">Удалить</el-link>
                </template>
            </div>
        </div>
    </div>
</div>
</template>
<script setup>

const t = defineModel();
function finishTask(t) {
    t.load = true;
    axios.get(route('calendar.task.finish', t.id))
        .then(() => t.finished_at = new Date().toISOString())
        .catch(() => t.load = true);
}

function deleteTask(t) {
    t.load = true;
    axios.delete(route('calendar.task.destroy', t.id))
        .then(() => t.deleted_at = new Date().toISOString())
        .catch(() => t.load = true);
}
</script>


<style>
.calendar-task {
    border-left: 5px solid;
    width: 100%;
    padding: 6px 8px;
    border-radius: 4px;
    background-color: #e9e7e7a1;
}

.calendar-task.success {
    border-color: green;
}

.calendar-task.wait {
    border-color: orange;
}

.calendar-task.destroy {
    border-color: red;
}
</style>