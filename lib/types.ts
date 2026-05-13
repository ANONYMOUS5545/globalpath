export type MembershipType = "FREE" | "PREMIUM" | "PREMIUM_PLUS";
export type AccessTier = "FREE" | "PREMIUM" | "PREMIUM_PLUS";
export type DegreeLevel = "UNDERGRADUATE" | "POSTGRADUATE" | "PHD" | "ALL";
export type CoverageType = "FULL" | "PARTIAL" | "FELLOWSHIP" | "EXCHANGE";
export type JobType = "FULL_TIME" | "PART_TIME" | "CONTRACT" | "INTERNSHIP" | "VOLUNTEER";
export type WorkplaceType = "REMOTE" | "ONSITE" | "HYBRID";
export type ApplicationStatus = "SUBMITTED" | "UNDER_REVIEW" | "ACCEPTED" | "REJECTED" | "WITHDRAWN";
export type ApplicationType = "SCHOLARSHIP" | "JOB" | "VISA";

export type Scholarship = {
  id: string;
  slug: string;
  title: string;
  provider: string;
  country: string;
  region: string;
  description: string;
  eligibility: string;
  benefits: string;
  requiredDocuments: string[];
  applicationProcess: string[];
  deadline: Date | null;
  officialUrl: string;
  sourceOrg?: string | null;
  fieldOfStudy?: string | null;
  degreeLevel: DegreeLevel;
  coverageType: CoverageType;
  accessTier: AccessTier;
  isFeatured: boolean;
  isActive: boolean;
  sourceRank: string;
  notes?: string | null;
};

export type Job = {
  id: string;
  slug: string;
  title: string;
  organization: string;
  location: string;
  country: string;
  description: string;
  requirements: string;
  salaryRange?: string | null;
  deadline: Date | null;
  officialUrl: string;
  sourceOrg?: string | null;
  jobType: JobType;
  workplaceType: WorkplaceType;
  sector: string;
  accessTier: AccessTier;
  isFeatured: boolean;
  isActive: boolean;
};

export type BlogPost = {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  category: string;
  authorName: string;
  readingTimeMinutes: number;
  isFeatured: boolean;
  isActive: boolean;
  publishedAt: Date | null;
};

export type JobResource = {
  id: string;
  resourceKey: string;
  title: string;
  organization: string;
  category: string;
  region: string;
  country?: string | null;
  resourceType: string;
  summary: string;
  applyUrl: string;
  applicationCostType: string;
  costNotes?: string | null;
  isFeatured: boolean;
  isActive: boolean;
  sortOrder: number;
};

export type AppUser = {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  phone?: string | null;
  country?: string | null;
  membershipType: MembershipType;
  membershipExpires?: Date | null;
  scholarshipAccess: boolean;
};

export type UserApplication = {
  id: string;
  type: ApplicationType;
  referenceId: string;
  status: ApplicationStatus;
  notes?: string | null;
  adminNotes?: string | null;
  createdAt: Date;
  title: string;
};

export type SelectOption = {
  label: string;
  value: string;
};
