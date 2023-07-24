import { defineStore } from "pinia";
import axios from "axios";
import { setFormError } from "@/helpers";

export interface Student {
    name: string;
    classroom: string;
    level: string;
    parent_contact: string;
}

export interface StudentState {
    students: Student[];
    total_rows: number;
    success_rows: number;
    current_page: number;
    last_page: number;
    total: number;
    pages: number[];
}

export const useStudentStore = defineStore("student", {
    state: (): StudentState => ({
        students: [],
        total_rows: 0,
        success_rows: 0,
        current_page: 1,
        last_page: 1,
        total: 0,
        pages: [],
    }),

    actions: {
        async upload(data: { [key: string]: any }): Promise<boolean> {
            const response = await axios.post("student/upload", data, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            switch (response.status) {
                case 200:
                    if (
                        response.data.status == "Success" &&
                        response.data.data.students.length > 0
                    ) {
                        for (const student of response.data.data.students) {
                            this.students.unshift(student);
                        }
                        this.total_rows = response.data.data.total_rows;
                        this.success_rows = response.data.data.success_rows;
                    } else {
                        this.total_rows = response.data.data.total_rows;
                        this.success_rows = response.data.data.success_rows;
                    }
                    return true;
                case 422:
                    return false;
                default:
                    return false;
            }
        },

        async index() {
            const response = await axios.get(
                `students?page=${this.current_page}`
            );

            switch (response.status) {
                case 200:
                    const { data, current_page, last_page, total } =
                        response.data.data.students;

                    this.current_page = current_page;
                    this.last_page = last_page;
                    this.total = total;
                    this.resetData();
                    for (const student of data) {
                        if (
                            this.students.filter((u) => u.id == student.id)
                                .length > 0
                        )
                            continue;
                        this.students.push(student);
                    }

                    this.pagination();
                    return true;
                default:
                    return false;
            }
        },

        pagination(page_size: number = 3): void {
            let pages: number[] = [];
            try {
                const numShown = Math.min(page_size, this.last_page);
                let first = this.current_page - Math.floor(numShown / 2);
                first = Math.max(first, 1);
                first = Math.min(first, this.last_page - numShown + 1);
                pages = [...Array(numShown)].map((k, i) => i + first);
            } catch (error) {
                console.log(error);
            }

            this.pages = pages.filter((p) => !isNaN(p));
        },

        setPage(page: number) {
            this.current_page = page;
            this.index();
        },

        resetData() {
            this.students = [];
        },
    },
});
