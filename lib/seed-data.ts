import type { BlogPost, Job, JobResource, Scholarship } from "./types";
import { slugify } from "./format";

export const brand = {
  name: "Global Path Africa",
  email: process.env.SITE_EMAIL ?? "info@globalpathafrica.org",
  whatsappNumber: process.env.WHATSAPP_NUMBER ?? "254792579974",
  whatsappUrl: `https://wa.me/${process.env.WHATSAPP_NUMBER ?? "254792579974"}`,
  colors: {
    navy: "#062B66",
    gold: "#D4A437",
    white: "#FFFFFF",
    gray: "#F7F7F7"
  }
};

function scholarship(data: Omit<Scholarship, "id" | "slug" | "isActive" | "region" | "sourceRank"> & { slug?: string; region?: string }) {
  return {
    id: data.slug ?? slugify(data.title),
    slug: data.slug ?? slugify(data.title),
    region: data.region ?? "Europe",
    isActive: true,
    sourceRank: "official",
    ...data
  } satisfies Scholarship;
}

function job(data: Omit<Job, "id" | "slug" | "isActive"> & { slug?: string }) {
  return {
    id: data.slug ?? slugify(data.title),
    slug: data.slug ?? slugify(data.title),
    isActive: true,
    ...data
  } satisfies Job;
}

export const fallbackScholarships = [
  scholarship({
    title: "ETH Zurich Excellence Scholarship & Opportunity Programme",
    provider: "ETH Zurich",
    country: "Switzerland",
    description:
      "A highly selective master's scholarship from ETH Zurich for outstanding students applying to eligible master's programmes. The award combines tuition support with a living-cost grant and access to the ETH Foundation network.",
    eligibility:
      "Outstanding academic record, admission to an eligible ETH Zurich master's programme, strong motivation, and documents submitted through the official ETH application process.",
    benefits: "Study and living-cost grant, tuition fee waiver, mentoring and access to ETH Foundation opportunities.",
    requiredDocuments: ["Bachelor transcript", "CV", "Motivation letter", "Pre-proposal where requested", "Reference letters"],
    applicationProcess: [
      "Select an eligible ETH Zurich master's programme.",
      "Prepare the ESOP-specific documents before submitting the master's application.",
      "Submit through ETH Zurich's official application portal before the programme deadline."
    ],
    deadline: new Date("2026-11-30T23:59:00.000Z"),
    officialUrl: "https://ethz.ch/en/studies/financial/scholarships/excellencescholarship.html",
    sourceOrg: "ETH Zurich",
    fieldOfStudy: "Multiple fields",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FULL",
    accessTier: "PREMIUM",
    isFeatured: true
  }),
  scholarship({
    title: "EPFL Excellence Fellowships for Master's Students",
    provider: "EPFL",
    country: "Switzerland",
    description:
      "EPFL offers excellence fellowships to exceptional master's applicants across science, engineering, technology, architecture and related disciplines.",
    eligibility:
      "Excellent academic profile, admission to an eligible EPFL master's programme, and a complete application submitted through the official EPFL admissions process.",
    benefits: "Financial fellowship paid over the master's programme, plus access to EPFL's international research environment.",
    requiredDocuments: ["Academic transcripts", "CV", "Statement of purpose", "Recommendation letters", "Programme-specific materials"],
    applicationProcess: [
      "Apply to the chosen EPFL master's programme.",
      "Indicate fellowship consideration where required in the application.",
      "Track official admissions communications from EPFL."
    ],
    deadline: new Date("2026-12-15T23:59:00.000Z"),
    officialUrl: "https://www.epfl.ch/education/studies/en/financing-study/grants/excellence-fellowships/",
    sourceOrg: "EPFL",
    fieldOfStudy: "STEM, architecture and technology",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FELLOWSHIP",
    accessTier: "PREMIUM",
    isFeatured: true
  }),
  scholarship({
    title: "TU Delft Justus & Louise van Effen Excellence Scholarships",
    provider: "Delft University of Technology",
    country: "Netherlands",
    description:
      "A prestigious scholarship for excellent international students admitted to TU Delft master's programmes, designed for applicants with a strong academic record and leadership promise.",
    eligibility:
      "Excellent bachelor's results, admission to a qualifying TU Delft master's programme, and a complete scholarship application submitted by the official deadline.",
    benefits: "Tuition fee support and contribution toward living expenses depending on scholarship allocation.",
    requiredDocuments: ["Bachelor transcript", "Motivation statement", "CV", "Reference letters", "English proficiency proof"],
    applicationProcess: [
      "Choose an eligible MSc programme at TU Delft.",
      "Upload scholarship documents with the MSc application.",
      "Submit before the scholarship deadline listed by TU Delft."
    ],
    deadline: new Date("2026-12-01T22:59:00.000Z"),
    officialUrl: "https://www.tudelft.nl/en/education/practical-matters/scholarships/justus-louise-van-effen-excellence-scholarships",
    sourceOrg: "TU Delft",
    fieldOfStudy: "Engineering, design, technology and applied sciences",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FULL",
    accessTier: "PREMIUM_PLUS",
    isFeatured: true
  }),
  scholarship({
    title: "Utrecht Excellence Scholarship",
    provider: "Utrecht University",
    country: "Netherlands",
    description:
      "A competitive scholarship for high-achieving international master's applicants admitted to eligible Utrecht University programmes.",
    eligibility:
      "Non-EU/EEA international applicant, excellent academic record, and admission to an eligible Utrecht University master's programme.",
    benefits: "Tuition fee award, sometimes combined with a contribution toward living expenses depending on faculty allocation.",
    requiredDocuments: ["Admission application", "Academic records", "Motivation statement", "CV", "Programme documents"],
    applicationProcess: [
      "Submit the master's application through Utrecht University.",
      "Complete the scholarship section in the applicant portal.",
      "Submit before the official faculty scholarship deadline."
    ],
    deadline: new Date("2027-02-01T23:59:00.000Z"),
    officialUrl: "https://www.uu.nl/en/masters/general-information/international-students/financial-matters/grants-and-scholarships/utrecht-excellence-scholarships",
    sourceOrg: "Utrecht University",
    fieldOfStudy: "Multiple fields",
    degreeLevel: "POSTGRADUATE",
    coverageType: "PARTIAL",
    accessTier: "FREE",
    isFeatured: false
  }),
  scholarship({
    title: "DAAD Development-Related Postgraduate Courses Scholarships",
    provider: "DAAD",
    country: "Germany",
    description:
      "DAAD funds development-related postgraduate study in Germany for professionals from developing and emerging countries, including many African applicants.",
    eligibility:
      "Bachelor's degree, relevant professional experience, development motivation, and admission to a participating postgraduate course in Germany.",
    benefits: "Monthly stipend, travel allowance, insurance, and tuition-related support depending on the selected course.",
    requiredDocuments: ["DAAD application form", "CV", "Motivation letter", "Academic transcripts", "Employment certificates", "Language proof"],
    applicationProcess: [
      "Select a DAAD EPOS-eligible programme.",
      "Check the course-specific deadline on the official DAAD list.",
      "Submit documents directly to the programme or DAAD route stated by the course."
    ],
    deadline: new Date("2026-10-31T23:59:00.000Z"),
    officialUrl: "https://www.daad.de/en/studying-in-germany/scholarships/daad-scholarships/",
    sourceOrg: "DAAD",
    fieldOfStudy: "Development, engineering, agriculture, economics, public policy",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FULL",
    accessTier: "FREE",
    isFeatured: true
  }),
  scholarship({
    title: "Sciences Po Emile Boutmy Scholarship",
    provider: "Sciences Po",
    country: "France",
    description:
      "Sciences Po's Emile Boutmy Scholarship supports excellent non-EU applicants admitted to undergraduate or master's programmes at the institution.",
    eligibility:
      "First-time non-EU applicant admitted to an eligible Sciences Po programme with strong academic and personal profile.",
    benefits: "Tuition support amount varies by degree level and applicant profile.",
    requiredDocuments: ["Admission application", "Academic transcripts", "Personal statement", "Financial documents where requested"],
    applicationProcess: [
      "Apply to the eligible Sciences Po programme.",
      "Indicate Emile Boutmy consideration in the admissions application.",
      "Upload all documents before the official scholarship deadline."
    ],
    deadline: new Date("2026-12-01T23:59:00.000Z"),
    officialUrl: "https://www.sciencespo.fr/students/en/fees-funding/bursaries-financial-aid/emile-boutmy-scholarship/",
    sourceOrg: "Sciences Po",
    fieldOfStudy: "Political science, public policy, international affairs and social sciences",
    degreeLevel: "ALL",
    coverageType: "PARTIAL",
    accessTier: "PREMIUM",
    isFeatured: false
  }),
  scholarship({
    title: "Swedish Institute Scholarships for Global Professionals",
    provider: "Swedish Institute",
    country: "Sweden",
    description:
      "A Swedish government scholarship programme for future global leaders admitted to eligible master's programmes in Sweden.",
    eligibility:
      "Citizen of an eligible country, documented leadership and work experience, and admission to an eligible Swedish master's programme.",
    benefits: "Full tuition coverage, monthly living allowance, insurance and travel grant for eligible recipients.",
    requiredDocuments: ["SI CV", "Motivation letter", "Proof of work experience", "Leadership proof", "University admissions documents"],
    applicationProcess: [
      "Apply to Swedish master's programmes through University Admissions.",
      "Prepare SI-specific leadership and work documents.",
      "Submit the SI scholarship application in the official SI portal."
    ],
    deadline: new Date("2027-02-26T23:59:00.000Z"),
    officialUrl: "https://si.se/en/apply/scholarships/swedish-institute-scholarships-for-global-professionals/",
    sourceOrg: "Swedish Institute",
    fieldOfStudy: "Multiple eligible master's programmes",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FULL",
    accessTier: "PREMIUM_PLUS",
    isFeatured: true
  }),
  scholarship({
    title: "University of Helsinki International Scholarship Programme",
    provider: "University of Helsinki",
    country: "Finland",
    description:
      "Merit-based scholarships for excellent non-EU/EEA applicants admitted to international master's programmes at the University of Helsinki.",
    eligibility:
      "Non-EU/EEA applicant liable for tuition fees, strong academic record and admission to an eligible international master's programme.",
    benefits: "Full or partial tuition fee scholarship, depending on award type.",
    requiredDocuments: ["Master's admission application", "Transcripts", "Motivation answers", "Language proof"],
    applicationProcess: [
      "Choose an international master's programme.",
      "Apply during the official University of Helsinki admissions period.",
      "Complete scholarship questions in the same application."
    ],
    deadline: new Date("2027-01-02T23:59:00.000Z"),
    officialUrl: "https://www.helsinki.fi/en/admissions-and-education/apply-bachelors-and-masters-programmes/scholarship-programme",
    sourceOrg: "University of Helsinki",
    fieldOfStudy: "Multiple fields",
    degreeLevel: "POSTGRADUATE",
    coverageType: "PARTIAL",
    accessTier: "FREE",
    isFeatured: false
  }),
  scholarship({
    title: "Science@Leuven Scholarships",
    provider: "KU Leuven",
    country: "Belgium",
    description:
      "Scholarships from the Faculty of Science at KU Leuven for talented international students applying to selected science master's programmes.",
    eligibility:
      "Excellent academic results, admission to a selected Faculty of Science master's programme, and complete scholarship file.",
    benefits: "Scholarship amount can cover tuition, insurance and living-cost contribution depending on award decision.",
    requiredDocuments: ["Admission proof or application", "Academic transcripts", "Motivation letter", "Recommendation letters"],
    applicationProcess: [
      "Apply to an eligible Faculty of Science master's programme.",
      "Complete the Science@Leuven scholarship application form.",
      "Submit all documents before the faculty deadline."
    ],
    deadline: new Date("2027-02-15T23:59:00.000Z"),
    officialUrl: "https://wet.kuleuven.be/english/scienceatleuvenscholarship",
    sourceOrg: "KU Leuven",
    fieldOfStudy: "Science master's programmes",
    degreeLevel: "POSTGRADUATE",
    coverageType: "PARTIAL",
    accessTier: "FREE",
    isFeatured: false
  }),
  scholarship({
    title: "Helmut Veith Stipend for Female Master's Students",
    provider: "TU Wien",
    country: "Austria",
    description:
      "A targeted stipend for outstanding women in computer science who are admitted to eligible English-taught master's programmes at TU Wien.",
    eligibility:
      "Female applicant with excellent background in computer science or mathematics and admission to an eligible TU Wien master's programme.",
    benefits: "Annual stipend and tuition fee waiver for eligible duration subject to academic progress.",
    requiredDocuments: ["CV", "Cover letter", "Motivation letter", "Transcripts", "Diplomas", "Reference letters"],
    applicationProcess: [
      "Review eligible TU Wien master's programmes.",
      "Prepare admission and stipend documents.",
      "Submit through the official stipend email or portal listed by TU Wien."
    ],
    deadline: new Date("2026-11-30T23:59:00.000Z"),
    officialUrl: "https://www.tuwien.at/en/tu-wien/organisation/central-divisions/international-office/scholarships-and-grants/helmut-veith-stipend",
    sourceOrg: "TU Wien",
    fieldOfStudy: "Computer science",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FELLOWSHIP",
    accessTier: "PREMIUM",
    isFeatured: false
  }),
  scholarship({
    title: "University of Copenhagen Danish Government Scholarships",
    provider: "University of Copenhagen",
    country: "Denmark",
    description:
      "Tuition and living-cost scholarships awarded by the University of Copenhagen to highly qualified non-EU/EEA master's applicants.",
    eligibility:
      "Non-EU/EEA applicant admitted to an eligible master's programme and liable for tuition fees.",
    benefits: "Full or partial tuition fee waiver and, in some cases, living-cost grant.",
    requiredDocuments: ["Programme application", "Academic transcripts", "CV", "Motivation documents where requested"],
    applicationProcess: [
      "Submit the master's programme application by the official admissions deadline.",
      "Eligible applicants are considered according to faculty scholarship rules.",
      "Watch the applicant portal for scholarship communications."
    ],
    deadline: new Date("2027-01-15T23:59:00.000Z"),
    officialUrl: "https://studies.ku.dk/masters/tuition-fees-scholarships/scholarships/",
    sourceOrg: "University of Copenhagen",
    fieldOfStudy: "Multiple fields",
    degreeLevel: "POSTGRADUATE",
    coverageType: "PARTIAL",
    accessTier: "FREE",
    isFeatured: false
  }),
  scholarship({
    title: "UCD Global Excellence Scholarship",
    provider: "University College Dublin",
    country: "Ireland",
    description:
      "A merit-based scholarship programme for high-achieving international students applying to eligible undergraduate or graduate degrees at UCD.",
    eligibility:
      "International applicant with strong academic achievement and admission or application to an eligible UCD programme.",
    benefits: "Tuition fee scholarship, commonly awarded as a percentage reduction depending on region and programme.",
    requiredDocuments: ["UCD application", "Academic transcripts", "Personal statement", "Offer or application reference"],
    applicationProcess: [
      "Apply to an eligible UCD programme.",
      "Submit the Global Excellence Scholarship application through UCD's official system.",
      "Use the region-specific deadline shown by UCD."
    ],
    deadline: new Date("2026-06-15T23:59:00.000Z"),
    officialUrl: "https://www.ucd.ie/global/scholarships/",
    sourceOrg: "University College Dublin",
    fieldOfStudy: "Multiple fields",
    degreeLevel: "ALL",
    coverageType: "PARTIAL",
    accessTier: "FREE",
    isFeatured: false
  }),
  scholarship({
    title: "NAWA Banach Scholarship Programme",
    provider: "Polish National Agency for Academic Exchange",
    country: "Poland",
    description:
      "A Polish government scholarship programme supporting master's study in Poland for applicants from selected countries, including many developing regions.",
    eligibility:
      "Eligible nationality, strong academic background, and application to an eligible Polish higher education programme.",
    benefits: "Monthly scholarship and tuition support according to NAWA rules.",
    requiredDocuments: ["NAWA application", "Academic documents", "Passport", "Study plan", "Language documents"],
    applicationProcess: [
      "Check eligibility and field rules on the official NAWA call.",
      "Create a profile in the NAWA ICT system.",
      "Submit all documents before the call deadline."
    ],
    deadline: new Date("2026-06-30T23:59:00.000Z"),
    officialUrl: "https://nawa.gov.pl/en/students/foreign-students/the-banach-scholarship-programme",
    sourceOrg: "NAWA",
    fieldOfStudy: "Engineering, agricultural sciences, exact sciences, life sciences and related fields",
    degreeLevel: "POSTGRADUATE",
    coverageType: "FULL",
    accessTier: "PREMIUM",
    isFeatured: false
  }),
  scholarship({
    title: "Bocconi Graduate Merit Awards",
    provider: "Bocconi University",
    country: "Italy",
    description:
      "Merit awards for outstanding international applicants admitted to graduate programmes at Bocconi University.",
    eligibility:
      "Excellent academic profile and admission to a qualifying Bocconi graduate programme.",
    benefits: "Tuition waiver or merit award depending on admission round and programme allocation.",
    requiredDocuments: ["Graduate application", "Transcripts", "CV", "Motivation documents", "Test scores where required"],
    applicationProcess: [
      "Apply to the selected Bocconi graduate programme.",
      "Submit by the relevant admissions round.",
      "Merit awards are assigned through the official admissions evaluation."
    ],
    deadline: new Date("2026-07-15T23:59:00.000Z"),
    officialUrl: "https://www.unibocconi.it/en/applying-bocconi/graduate/fees-funding/scholarships-and-funding",
    sourceOrg: "Bocconi University",
    fieldOfStudy: "Economics, management, finance, law, data science and social sciences",
    degreeLevel: "POSTGRADUATE",
    coverageType: "PARTIAL",
    accessTier: "PREMIUM",
    isFeatured: false
  }),
  scholarship({
    title: "University of Oslo International Master's Scholarships",
    provider: "University of Oslo",
    country: "Norway",
    description:
      "Competitive faculty and programme-linked scholarship opportunities for selected international master's applicants at the University of Oslo.",
    eligibility:
      "Strong academic record, admission to an eligible master's programme, and fulfilment of programme-specific scholarship criteria.",
    benefits: "Funding varies by faculty and call, often tied to tuition or living-cost support.",
    requiredDocuments: ["Admission application", "Academic transcripts", "Motivation letter", "Programme-specific documents"],
    applicationProcess: [
      "Choose an eligible master's programme at the University of Oslo.",
      "Review programme and faculty scholarship notes.",
      "Submit admission and scholarship documents by the official deadline."
    ],
    deadline: new Date("2026-12-01T23:59:00.000Z"),
    officialUrl: "https://www.uio.no/english/studies/admission/master/",
    sourceOrg: "University of Oslo",
    fieldOfStudy: "Multiple fields",
    degreeLevel: "POSTGRADUATE",
    coverageType: "PARTIAL",
    accessTier: "PREMIUM_PLUS",
    isFeatured: false
  }),
  scholarship({
    title: "IE Foundation Scholarships",
    provider: "IE University",
    country: "Spain",
    description:
      "IE Foundation scholarships support talented international applicants across undergraduate and master's programmes at IE University.",
    eligibility:
      "Strong academic and personal profile, admission to IE University, and demonstrated fit with scholarship criteria.",
    benefits: "Partial tuition scholarship depending on programme, profile and funding round.",
    requiredDocuments: ["Admission application", "Scholarship application", "Academic documents", "Financial information where requested"],
    applicationProcess: [
      "Apply for admission to IE University.",
      "Complete the scholarship application in the official IE portal.",
      "Submit early because awards are competitive and funds are limited."
    ],
    deadline: new Date("2027-05-01T23:59:00.000Z"),
    officialUrl: "https://www.ie.edu/financial-aid/scholarships/",
    sourceOrg: "IE University",
    fieldOfStudy: "Business, law, international relations, technology and design",
    degreeLevel: "ALL",
    coverageType: "PARTIAL",
    accessTier: "FREE",
    isFeatured: false
  })
];

export const fallbackJobs = [
  job({
    title: "Remote Customer Success Specialist",
    organization: "GitLab",
    location: "Remote - EMEA",
    country: "Remote",
    description:
      "Support global customers using a remote-first workflow, coordinate renewals, document account risks and help users succeed with modern software collaboration tools.",
    requirements:
      "Strong written communication, customer-facing experience, comfort working asynchronously and ability to support international accounts.",
    salaryRange: null,
    deadline: new Date("2026-08-31T23:59:00.000Z"),
    officialUrl: "https://about.gitlab.com/jobs/",
    sourceOrg: "GitLab Careers",
    jobType: "FULL_TIME",
    workplaceType: "REMOTE",
    sector: "Technology",
    accessTier: "FREE",
    isFeatured: true
  }),
  job({
    title: "Programme Associate, Education and Youth",
    organization: "UNESCO",
    location: "Paris, France",
    country: "France",
    description:
      "Coordinate programme documentation, partner communication and reporting for education and youth initiatives in an international public-sector environment.",
    requirements:
      "Bachelor's degree, programme administration experience, excellent English and French communication, and strong reporting skills.",
    salaryRange: null,
    deadline: new Date("2026-07-30T23:59:00.000Z"),
    officialUrl: "https://careers.unesco.org/",
    sourceOrg: "UNESCO Careers",
    jobType: "FULL_TIME",
    workplaceType: "ONSITE",
    sector: "Education",
    accessTier: "PREMIUM",
    isFeatured: true
  }),
  job({
    title: "Clinical Research Coordinator",
    organization: "Médecins Sans Frontières",
    location: "Brussels, Belgium",
    country: "Belgium",
    description:
      "Support clinical research coordination, ethics documentation, field communication and trial administration for humanitarian health programmes.",
    requirements:
      "Health sciences background, research coordination experience, strong documentation practice and ability to work in multicultural teams.",
    salaryRange: null,
    deadline: new Date("2026-09-15T23:59:00.000Z"),
    officialUrl: "https://www.msf.org/careers",
    sourceOrg: "MSF Careers",
    jobType: "CONTRACT",
    workplaceType: "HYBRID",
    sector: "Healthcare",
    accessTier: "PREMIUM_PLUS",
    isFeatured: false
  }),
  job({
    title: "Remote Data Analyst",
    organization: "Canonical",
    location: "Remote - Global",
    country: "Remote",
    description:
      "Build dashboards, analyse product and business metrics, and support distributed teams with reliable reporting and decision-ready insights.",
    requirements:
      "SQL, spreadsheet fluency, analytical writing, stakeholder communication and experience with remote teams.",
    salaryRange: null,
    deadline: new Date("2026-10-20T23:59:00.000Z"),
    officialUrl: "https://canonical.com/careers",
    sourceOrg: "Canonical Careers",
    jobType: "FULL_TIME",
    workplaceType: "REMOTE",
    sector: "Technology",
    accessTier: "FREE",
    isFeatured: false
  }),
  job({
    title: "Graduate Trainee, International Development",
    organization: "World Bank Group",
    location: "Washington DC, United States",
    country: "United States",
    description:
      "Assist teams with research, portfolio reviews, development data and project coordination across global development programmes.",
    requirements:
      "Master's degree in economics, public policy, finance, development or related discipline; strong research and writing skills.",
    salaryRange: null,
    deadline: new Date("2026-08-15T23:59:00.000Z"),
    officialUrl: "https://www.worldbank.org/en/about/careers",
    sourceOrg: "World Bank Careers",
    jobType: "FULL_TIME",
    workplaceType: "ONSITE",
    sector: "International Development",
    accessTier: "PREMIUM",
    isFeatured: true
  }),
  job({
    title: "Remote Talent Sourcer",
    organization: "Deel",
    location: "Remote - EMEA",
    country: "Remote",
    description:
      "Source international talent, maintain candidate pipelines and partner with recruiting teams across remote-first hiring markets.",
    requirements:
      "Recruiting or sourcing experience, structured outreach skills, ATS discipline and strong candidate communication.",
    salaryRange: null,
    deadline: new Date("2026-11-10T23:59:00.000Z"),
    officialUrl: "https://www.deel.com/careers/",
    sourceOrg: "Deel Careers",
    jobType: "FULL_TIME",
    workplaceType: "REMOTE",
    sector: "Remote Work",
    accessTier: "PREMIUM",
    isFeatured: false
  })
];

export const fallbackBlogPosts: BlogPost[] = [
  {
    id: "scholarship-application-pack",
    title: "Build a Strong International Scholarship Application Pack",
    slug: "build-a-strong-international-scholarship-application-pack",
    excerpt: "A concise checklist for preparing transcripts, essays, references and proof documents before deadlines arrive.",
    content:
      "Strong applications are built before portals open. Prepare a clean CV, academic records, a reusable motivation draft, reference contacts and passport details early. Store everything as clearly named PDFs and keep a deadline tracker for each university.",
    category: "guides",
    authorName: "Global Path Africa Team",
    readingTimeMinutes: 4,
    isFeatured: true,
    isActive: true,
    publishedAt: new Date("2026-05-01T09:00:00.000Z")
  },
  {
    id: "free-vs-premium-opportunities",
    title: "How Free and Premium Opportunity Access Works",
    slug: "how-free-and-premium-opportunity-access-works",
    excerpt: "Why some opportunities remain free while advanced, high-touch listings sit behind premium membership.",
    content:
      "Free access should still be useful. Premium access is reserved for deeper research, competitive funding, advanced job listings and extra support. This keeps discovery open while funding the work required to maintain quality.",
    category: "membership",
    authorName: "Global Path Africa Team",
    readingTimeMinutes: 3,
    isFeatured: false,
    isActive: true,
    publishedAt: new Date("2026-04-28T09:00:00.000Z")
  }
];

export const fallbackJobResources: JobResource[] = [
  {
    id: "we-work-remotely",
    resourceKey: "we-work-remotely",
    title: "We Work Remotely",
    organization: "We Work Remotely",
    category: "remote_jobs",
    region: "Global",
    country: "Remote",
    resourceType: "remote_job_board",
    summary: "Remote-first job board with public listings across technology, support, marketing and operations.",
    applyUrl: "https://weworkremotely.com/",
    applicationCostType: "free",
    costNotes: "Listings are publicly browseable. Always confirm the employer domain before sharing documents.",
    isFeatured: true,
    isActive: true,
    sortOrder: 10
  },
  {
    id: "reliefweb-jobs",
    resourceKey: "reliefweb-jobs",
    title: "ReliefWeb Jobs",
    organization: "United Nations OCHA",
    category: "onsite_jobs",
    region: "Global",
    country: "Global",
    resourceType: "official_humanitarian_board",
    summary: "Humanitarian and development vacancies from NGOs, UN agencies and international organisations.",
    applyUrl: "https://reliefweb.int/jobs",
    applicationCostType: "free",
    costNotes: "Use official employer application links from each posting and avoid third-party payment requests.",
    isFeatured: true,
    isActive: true,
    sortOrder: 20
  },
  {
    id: "un-careers",
    resourceKey: "un-careers",
    title: "UN Careers",
    organization: "United Nations",
    category: "onsite_jobs",
    region: "Global",
    country: "Global",
    resourceType: "official_employer",
    summary: "Official United Nations careers portal for international professional, field, consultant and internship roles.",
    applyUrl: "https://careers.un.org/",
    applicationCostType: "free",
    costNotes: "The United Nations does not charge applicants at any recruitment stage.",
    isFeatured: true,
    isActive: true,
    sortOrder: 30
  }
];

export const africanCountries = [
  { name: "Algeria", code: "DZ", region: "North Africa" },
  { name: "Angola", code: "AO", region: "Central Africa" },
  { name: "Benin", code: "BJ", region: "West Africa" },
  { name: "Botswana", code: "BW", region: "Southern Africa" },
  { name: "Burkina Faso", code: "BF", region: "West Africa" },
  { name: "Burundi", code: "BI", region: "East Africa" },
  { name: "Cameroon", code: "CM", region: "Central Africa" },
  { name: "Côte d'Ivoire", code: "CI", region: "West Africa" },
  { name: "Egypt", code: "EG", region: "North Africa" },
  { name: "Ethiopia", code: "ET", region: "East Africa" },
  { name: "Ghana", code: "GH", region: "West Africa" },
  { name: "Kenya", code: "KE", region: "East Africa" },
  { name: "Morocco", code: "MA", region: "North Africa" },
  { name: "Nigeria", code: "NG", region: "West Africa" },
  { name: "Rwanda", code: "RW", region: "East Africa" },
  { name: "Senegal", code: "SN", region: "West Africa" },
  { name: "South Africa", code: "ZA", region: "Southern Africa" },
  { name: "Tanzania", code: "TZ", region: "East Africa" },
  { name: "Uganda", code: "UG", region: "East Africa" },
  { name: "Zambia", code: "ZM", region: "Southern Africa" },
  { name: "Zimbabwe", code: "ZW", region: "Southern Africa" }
];
