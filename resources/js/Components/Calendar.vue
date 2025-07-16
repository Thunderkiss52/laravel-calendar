<script setup lang="ts">
import { computed, onBeforeMount, onMounted, reactive, ref, watch } from 'vue'
import * as Icons from '@element-plus/icons-vue'
import axios from 'axios';
import TaskWidget from './TaskWidget.vue'; 
import { ElMessage } from 'element-plus';


const props = defineProps({
    emptyText: {
        type: String,
        default: "Нет данных"
    },
    markers: {
        type: Array,
        default: []
    }
});

let week_day_names = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
let monthes = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
let loadTasksDay = ref(false);
let loadMonthTasks = ref(false);
let loadWeekTasks = ref(false);
let dayTasks = ref({});
let selectCalendar = ref(new Date(new Date().getTime() + 3600 * 1000));
let selectWeek = ref(null);
let tasks_day = ref([]);
let weekTasks = [];
let taskDialog = ref(false);
let showMonth = ref(true);
let tasks = [];
let tasks_week = [];
let selectYear = new Date().getFullYear();
let selectMonth = new Date().getMonth();
let years = [2023, 2024, 2025];

onBeforeMount(() => {
    var offset = +3;
    setNowWeek();
});

const dayHasTasks = computed(() => {
    let exist = false;
    Object.keys(tasks_day.value).forEach(k => {
        if(tasks_day.value[k].length > 0) {
            exist = true;
        }
    });
    return exist;
} );

onMounted(() => updateAll());


function setNowWeek() {
    selectWeek.value = new Date();
    selectWeek.value.setDate(selectWeek.value.getDate() - selectWeek.value.getDay() + 1);
}
function addSelectWeekDays(days) {
    let date = selectWeek;
    date.value.setDate(selectWeek.value.getDate() + days);
    selectWeek.value = new Date(date.value);
}

watch(selectCalendar, (old, newval) => {
    if (newval instanceof Date) {
        if (old.getMonth() != newval.getMonth()) {
            updateDayTasks();
        }
    }
    updateTasksDay();
});

watch(selectWeek, (old, newval) => {
    updateTasksWeek();
});


function updateAll() {
    updateDayTasks();
    updateTasksWeek();
    updateTasksDay();
}

let controller = new AbortController();
let controller2 = new AbortController();
let controller3 = new AbortController();

function updateDayTasks() {
    controller.abort();
    controller = new AbortController();
    loadMonthTasks.value = false;
    dayTasks.value = {};
    axios.get("calendar/month/" + selectCalendar.value.getFullYear() + "/" + (selectCalendar.value.getMonth() + 1), {
        signal: controller.signal,
    })
        .then((response) => {
            dayTasks.value = response.data;
            loadMonthTasks.value = true;
        })
        .catch((error) => {
            if(error.response && error.response.data && error.response.data.message) {
            ElMessage({
                showClose: true,
                duration: 0,
                message: error.response.data.message,
                type: 'error'
            });
            }
        });
}
function updateTasksWeek() {
    controller2.abort();
    controller2 = new AbortController();
    tasks_week = [];
    loadWeekTasks.value = false;
    axios.get("calendar/week/" + selectWeek.value.toISOString(), {
        signal: controller2.signal,
    }).then((response) => {
        tasks_week = response.data;
        loadWeekTasks.value = true;
    }).catch((error) => {
            if(error.response && error.response.data && error.response.data.message) {
        ElMessage({
            showClose: true,
            duration: 0,
            message: error.response.data.message,
            type: 'error'
        });
        }
    });
}
function updateTasksDay() {
    controller3.abort();
    controller3 = new AbortController();
    tasks_day.value = [];
    loadTasksDay.value = false;
    axios.get("calendar/day/" + selectCalendar.value.toLocaleDateString(), {
        signal: controller3.signal,
    }).then((response) => {
        tasks_day.value = response.data;
        loadTasksDay.value = true;
    }).catch((error) => {
            if(error.response && error.response.data && error.response.data.message) {
        ElMessage({
            showClose: true,
            duration: 0,
            message: error.response.data.message,
            type: 'error'
        });
        }
    });
}

</script>

<template>
    <el-row :gutter="10">
            <el-col :md="13">
                <el-card v-loading="!loadMonthTasks" body-style="padding: 5px">
                    <el-calendar v-model="selectCalendar">
                        <template #date-cell="{ date, data }">
                            <div
                                style="display: flex; flex-direction: column; gap: 4px; justify-content: start; height: 100%;">
                                <div style="display: flex; justify-content: space-between;">
                                    <h2 v-if="data.isSelected" class="is-selected">
                                        <el-tag disable-transitions effect="dark" type="warning" size="small">{{
                                            data.day.split('-').slice(2).join('-') }} <i
                                                v-if="data.isSelected"></i></el-tag>
                                    </h2>

                                    <h2 v-else>
                                        {{ data.day.split('-').slice(2).join('-') }}
                                    </h2>

                                    <template v-if="dayTasks[data.day] && dayTasks[data.day].holidays && dayTasks[data.day].holidays != null">
                                            
                                        <el-icon>
                                            <Icons.Clock v-if="dayTasks[data.day].holidays.isShort" />
                                            <Icons.House v-else />
                                        </el-icon>
                                    </template>
                                </div>
                                <div style="display: flex; gap: 3px; flex-wrap: wrap;">
                                    <template v-for="(marker, mkey) in markers">
                                        <el-tag size="small" disable-transitions v-if="dayTasks[data.day] && Object.keys(dayTasks[data.day]).includes(mkey) && dayTasks[data.day][mkey] > 0" :type="marker.type" effect="dark"><el-icon>
                                            <component :is="Icons[marker.icon]" />
                                        </el-icon> {{ dayTasks[data.day][mkey] }}</el-tag>
                                    </template>
                                    
                                    <span v-if="dayTasks[data.day] && dayTasks[data.day].holidays && dayTasks[data.day].holidays != null"
                                                style="font-size:11px; word-break: break-all">
                                                {{ dayTasks[data.day].holidays.name }}</span>
                                </div>
                            </div>
                        </template>
                    </el-calendar>
                </el-card>
            </el-col>
            <el-col :md="11">
                <el-card style="margin-bottom: 10px;">
                    <h1>День: {{ selectCalendar.toLocaleDateString("ru-RU") }}</h1>

                </el-card>
                <el-card v-if="!loadTasksDay">
                    <el-skeleton :rows="5" animated />
                </el-card>
                <template v-else>
                    <TaskWidget :date="selectCalendar" :tasks="tasks_day.tasks" class="mb-3" />
                    <el-card v-if="tasks_day.holidays" class="mb-3">
                        <el-row>
                            <el-col :span="4">
                                <el-image style="width: 60px" :src="`/img/holiday.gif`"
                                    fit="contain">
                                    <template #placeholder>
                                        <el-skeleton animated>
                                            <template #template>
                                                <el-skeleton-item variant="image"
                                                    style="width: 100%; height: 50px" />
                                            </template>
                                        </el-skeleton>
                                    </template>
                                    <template #error>
                                        <el-skeleton>
                                            <template #template>
                                                <el-skeleton-item variant="image"
                                                    style="width: 100%; height: 50px" />
                                            </template>
                                        </el-skeleton>
                                    </template>
                                </el-image>
                            </el-col>
                            <el-col :span="20">
                                <h1 style="padding-bottom: 5px">{{ tasks_day.holidays.name }}</h1>
                                <p>
                                    <el-text v-if="tasks_day.holidays.isShort">Сокращённый день</el-text>
                                    <el-text v-else>Выходной</el-text>
                                </p>
                            </el-col>
                        </el-row>
                    </el-card>
                    <template v-for="t in Object.keys(tasks_day)">
                        <slot :item="task" v-for="task in tasks_day[t]" :name="t" />
                    </template>
                    <el-card v-if="!dayHasTasks">
                        <el-empty :description="props.emptyText" />
                    </el-card>
                </template>
            </el-col>
    </el-row>
</template>