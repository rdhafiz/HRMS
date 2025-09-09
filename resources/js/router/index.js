import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

import Login from '../pages/Auth/Login.vue'
import ForgotPassword from '../pages/Auth/ForgotPassword.vue'
import ResetPassword from '../pages/Auth/ResetPassword.vue'
import Dashboard from '../pages/Dashboard/Dashboard.vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import Departments from '../pages/Employment/Departments/Departments.vue'
import DepartmentForm from '../pages/Employment/Departments/DepartmentForm.vue'
import Designations from '../pages/Employment/Designations/Designations.vue'
import DesignationForm from '../pages/Employment/Designations/DesignationForm.vue'
import Employees from '../pages/Employment/Employees/Employees.vue'
import EmployeeForm from '../pages/Employment/Employees/EmployeeForm.vue'
import EmployeeView from '../pages/Employment/Employees/EmployeeView.vue'
import DailyAttendance from '../pages/Attendance/DailyAttendance.vue'
import LeaveRequests from '../pages/Attendance/LeaveRequests.vue'
import LeaveRequestForm from '../pages/Attendance/LeaveRequestForm.vue'
import Holidays from '../pages/Attendance/Holidays.vue'
import HolidayForm from '../pages/Attendance/HolidayForm.vue'
import SalaryStructures from '../pages/Payroll/SalaryStructures.vue'
import SalaryStructureForm from '../pages/Payroll/SalaryStructureForm.vue'
import AssignSalaryStructure from '../pages/Payroll/AssignSalaryStructure.vue'
import EmployeeSalaryView from '../pages/Payroll/EmployeeSalaryView.vue'
import Admins from '../pages/Admins/Admins.vue'
import AdminForm from '../pages/Admins/AdminForm.vue'
import AdminProfile from '../pages/Profile/AdminProfile.vue'
import UpdateProfile from '../pages/Profile/UpdateProfile.vue'
import ChangePassword from '../pages/Profile/ChangePassword.vue'

axios.defaults.withCredentials = true
axios.defaults.baseURL = '/api'

let cachedUser = null
async function getUser() {
  if (cachedUser) return cachedUser
  const { data } = await axios.get('/auth/user')
  cachedUser = data
  return cachedUser
}

const routes = [
  {
    path: '/',
    component: AuthLayout,
    children: [
      { path: '', name: 'login', component: Login },
      { path: 'forgot', name: 'forgot', component: ForgotPassword },
      { path: 'reset', name: 'reset', component: ResetPassword },
    ],
  },
  {
    path: '/',
    component: DashboardLayout,
    children: [
      { path: 'dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      // Employment Management
      { path: 'departments', name: 'departments', component: Departments, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'departments/create', name: 'departments.create', component: DepartmentForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'departments/:id/edit', name: 'departments.edit', component: DepartmentForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'designations', name: 'designations', component: Designations, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'designations/create', name: 'designations.create', component: DesignationForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'designations/:id/edit', name: 'designations.edit', component: DesignationForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      // Employees
      { path: 'employees', name: 'employees', component: Employees, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'employees/create', name: 'employees.create', component: EmployeeForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'employees/:id/edit', name: 'employees.edit', component: EmployeeForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'employees/:id', name: 'employees.view', component: EmployeeView, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      // Attendance
      { path: 'attendance/daily', name: 'attendance.daily', component: DailyAttendance, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'attendance/leaves', name: 'attendance.leaves', component: LeaveRequests, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'attendance/leaves/create', name: 'attendance.leaves.create', component: LeaveRequestForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'attendance/leaves/:id/edit', name: 'attendance.leaves.edit', component: LeaveRequestForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'attendance/holidays', name: 'attendance.holidays', component: Holidays, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'attendance/holidays/create', name: 'attendance.holidays.create', component: HolidayForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'attendance/holidays/:id/edit', name: 'attendance.holidays.edit', component: HolidayForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      // Admins (System Admin only - front-end guard via user fetch)
      { path: 'admins', name: 'admins', component: Admins, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN'] } },
      { path: 'admins/create', name: 'admins.create', component: AdminForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN'] } },
      { path: 'admins/:id/edit', name: 'admins.edit', component: AdminForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN'] } },
      // Payroll - Salary Structures
      { path: 'payroll/salary-structures', name: 'payroll.structures', component: SalaryStructures, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'payroll/salary-structures/create', name: 'payroll.structures.create', component: SalaryStructureForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'payroll/salary-structures/:id/edit', name: 'payroll.structures.edit', component: SalaryStructureForm, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'employees/:id/assign-salary', name: 'employees.assignSalary', component: AssignSalaryStructure, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER'] } },
      { path: 'employees/:id/salary', name: 'employees.salary', component: EmployeeSalaryView, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      // Profile
      { path: 'profile', name: 'profile', component: AdminProfile, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'profile/update', name: 'profile.update', component: UpdateProfile, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
      { path: 'profile/change-password', name: 'profile.password', component: ChangePassword, meta: { requiresAuth: true, roles: ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR'] } },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  if (!to.meta.requiresAuth) return next()
  try {
    const user = await getUser()
    const roles = to.meta.roles || ['SYSTEM ADMIN','HR MANAGER','SUPERVISOR']
    if (roles.includes(user.admin_type_label)) {
      return next()
    }
    return next({ name: 'dashboard' })
  } catch (e) {
    return next({ name: 'login' })
  }
})

export default router

