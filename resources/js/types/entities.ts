// Auto-generated — do not edit
// Generated: 2026-05-12T03:31:04+00:00

export interface ActivityEntity {
  id?: number | null;
  user_id: number;
  project_id: number;
  task_category_id: number;
  date: string;
  duration: number;
  notes?: string | null;
}

export interface ActivityLogEntity {
  id?: number | null;
  daily_log_id: number;
  activity_type_id?: number | null;
  task_id?: number | null;
  expense_id?: number | null;
  start_offset_seconds?: number | null;
  end_offset_seconds?: number | null;
  duration_seconds?: number | null;
  notes?: string | null;
}

export interface ClockEntryEntity {
  daily_log_id: string | number;
  in?: string | null;
  timezone?: string | null;
  duration_seconds?: number | null;
  out?: string | null;
  applied_rate?: number | null;
  amount?: number | null;
  currency?: string | null;
  client_id?: string | null;
  rate_id?: string | number | null;
  notes?: string | null;
  id?: string | number | null;
}

export interface DailyLogEntity {
  user_id: string | number;
  project_id: string | number;
  date: string;
  id?: string | number | null;
  total_seconds?: number;
}

export interface GitLogEntity {
  id: number;
  repository_id: string;
  commit_hash: string;
  author_name: string;
  author_email: string;
  message: string;
  diff: string;
  user_id: string | number;
  committed_at: string;
}

export interface MonitoredActivityEntity {
  id?: number | null;
  daily_log_id: number;
  process: string;
  window_title?: string | null;
  timestamp: string;
}

export interface ProjectEntity {
  status: string;
  name: string;
  type: string;
  id?: number | null;
  organization_id?: number | null;
  description?: string | null;
  start_date?: string | null;
  end_date?: string | null;
  location?: string | null;
  icon?: string | null;
  default_break_duration_seconds?: string | null;
  metadata?: any[] | null;
}

export interface TaskEntity {
  id?: number | null;
  project_id: number;
  name: string;
  description?: string | null;
  priority: string;
  completed: boolean;
}

export interface BudgetAdjustmentEntity {
  id?: number | null;
  budget_id: number;
  user_id: number;
  adjustment_amount: number;
  adjustment_amount_low?: number | null;
  adjustment_amount_high?: number | null;
  currency: string;
  reason?: string | null;
}

export interface BudgetEntity {
  id?: number | null;
  project_id: number;
  amount: number;
  amount_low?: number | null;
  amount_high?: number | null;
  currency: string;
  type: string;
  status: string;
  allocated_hours?: number | null;
  start_date: string;
  end_date?: string | null;
}

export interface ExpenseEntity {
  id?: number | null;
  project_id: number;
  budget_id: number;
  user_id: number;
  amount: number;
  currency: string;
  description?: string | null;
  status: string;
  expense_date: string;
}

export interface RateEntity {
  id?: number | null;
  rate_type_id?: number | null;
  organization_id?: number | null;
  project_id?: number | null;
  user_id?: number | null;
  rate_frequency: string;
  rate_type?: string | null;
  amount: number;
  currency: string;
  overtime_multiplier: number;
  holiday_multiplier: number;
  special_multiplier: number;
  custom_multiplier_rate?: number | null;
  custom_multiplier_label?: string | null;
  is_default: boolean;
  effective_from?: string | null;
  effective_until?: string | null;
}

export interface UserEntity {
  id?: string | number | null;
  workos_id?: string | null;
  handle?: string | null;
  name?: string | null;
  first_name?: string | null;
  last_name?: string | null;
  email?: string | null;
  avatar?: string | null;
  timezone?: string;
  preferences?: any[] | null;
  cli_config?: any[] | null;
  current_team_id?: string | number | null;
  org_id?: string | number | null;
  password?: string | null;
  created_at?: string | null;
  updated_at?: string | null;
  email_verified_at?: string | null;
  todays_entries?: any[] | null;
  roles?: any[] | null;
  projects?: any[] | null;
  organizations?: any[] | null;
  permissions?: any[] | null;
}

export interface BranchEntity {
  id?: number | null;
  repository_id: number;
  name: string;
  is_default: boolean;
}

export interface CodeRepositoryEntity {
  id?: number | null;
  user_id: number;
  github_connection_id?: number | null;
  project_id?: number | null;
  name: string;
  url: string;
  path?: string | null;
}

export interface GithubConnectionEntity {
  id?: number | null;
  user_id: number;
  github_user_id: string;
  username: string;
  account_handle?: string | null;
  avatar_url?: string | null;
  access_token?: string | null;
  refresh_token?: string | null;
  token_expires_at?: string | null;
}

export interface GoogleConnectionEntity {
  id?: number | null;
  user_id: number;
  google_user_id: string;
  email: string;
  access_token?: string | null;
  refresh_token?: string | null;
  token_expires_at?: string | null;
}

export interface RepositorySettingsEntity {
  id?: number | null;
  repository_id: number;
  branch_id?: number | null;
  excluded_folders: any[];
  excluded_file_extensions: any[];
}

export interface OrganizationEntity {
  id?: number | null;
  userId?: number | null;
  name: string;
  description?: string | null;
  type: string;
  icon?: string | null;
  metadata?: any[] | null;
}

export interface TeamEntity {
  id?: number | null;
  name: string;
  slug: string;
  is_personal: boolean;
}

export interface TeamInvitationEntity {
  id?: number | null;
  code: string;
  team_id: number;
  email: string;
  role: string;
  invited_by: number;
  expires_at?: string | null;
  accepted_at?: string | null;
}

export interface TeamMemberEntity {
  id?: number | null;
  team_id: number;
  user_id: number;
  role: string;
}

export interface ActivityTypeAliasEntity {
  id?: number | null;
  activity_type_id: number;
  organization_id?: number | null;
  user_id?: number | null;
  alias: string;
}

export interface ActivityTypeEntity {
  id?: number | null;
  name: string;
  description?: string | null;
  color?: string | null;
  icon?: string | null;
}

export interface TaskCategoryAliasEntity {
  id?: number | null;
  task_category_id: number;
  organization_id?: number | null;
  user_id?: number | null;
  alias: string;
}

export interface TaskCategoryEntity {
  id?: number | null;
  name: string;
  description?: string | null;
}
