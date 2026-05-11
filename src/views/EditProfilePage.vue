<script setup lang="ts">
import { onBeforeUnmount, onMounted, reactive, ref, nextTick, computed, watch } from "vue";
import { useRouter } from "vue-router";
import { Camera, CameraResultType, CameraSource } from "@capacitor/camera";
import { Capacitor } from "@capacitor/core";
import { parsePhoneNumberFromString, type CountryCode } from "libphonenumber-js";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/vue-tel-input.css";
import AppNavbar from "@/components/AppNavbar.vue";
import { fetchCurrentUser, logout, updateProfile, uploadProfilePicture, type AuthUser } from "@/services/auth";
import { useTheme } from "@/composables/useTheme";

const router = useRouter();
const pageContentId = "edit-profile-content";
const loading = ref(true);
const saving = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const phoneCountry = ref("PK");
const selectedPicture = ref<File | null>(null);
const user = ref<AuthUser | null>(null);
const previewUrl = ref("");
const cameraInput = ref<HTMLInputElement | null>(null);
const penRef = ref<HTMLElement | null>(null);
const pictureMenuRef = ref<HTMLElement | null>(null);
const menuPosition = ref({ top: '0px', left: '0px' });
const menuBelow = ref(false);
// Professional step skills state
const skillsArray = ref<string[]>([]);
const skillInput = ref('');
const skillsInputRef = ref<HTMLInputElement | null>(null);
const showSkillsSuggestions = ref(false);
const skillSuggestions = [
  "Community Volunteering",
  "Social Work",
  "Fundraising",
  "Donor Relations",
  "Event Management",
  "Education & Teaching",
  "Career Counseling",
  "Student Mentoring",
  "Healthcare Support",
  "Medical Consultation",
  "Mental Health Support",
  "Legal Aid",
  "Financial Assistance Coordination",
  "Business & Entrepreneurship",
  "Job Placement Support",
  "IT & Computer Skills",
  "Software Development",
  "Graphic Design",
  "Video Editing",
  "Photography",
  "Content Writing",
  "Social Media Management",
  "Public Speaking",
  "Leadership",
  "Project Management",
  "Data Entry",
  "Office Administration",
  "Translation",
  "Religious & Ethical Guidance",
  "Emergency Response"
];
// Community step state
const interestsList = ['Welfare','Education','IT Support','Health','Business','Other'];
const shortBioMax = 200;
const shortBioCount = ref(0);
// Custom dropdown state to avoid native overflow and match design
const professionOpen = ref(false);
const experienceOpen = ref(false);
const professionRef = ref<HTMLElement | null>(null);
const experienceRef = ref<HTMLElement | null>(null);
const professionPanelUp = ref(false);
const experiencePanelUp = ref(false);
const professionOptions = ['Software Engineer','Product Manager','Designer','Data Scientist','Other'];
const experienceOptions = ['0-1 Years','1-3 Years','3+ Years','5+ Years'];

// Community custom select state (to match Step 2 custom selects)
const roleOpen = ref(false);
const bloodOpen = ref(false);
const roleRef = ref<HTMLElement | null>(null);
const bloodRef = ref<HTMLElement | null>(null);
const rolePanelUp = ref(false);
const bloodPanelUp = ref(false);
const roleOptions = ['Volunteer','Member','Coordinator','Organizer'];
const bloodOptions = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];

// Utility to detect mobile device
function isMobileDevice() {
  return /Mobi|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
}

// Webcam modal state (optional, for desktop/laptop)
const showWebcamModal = ref(false);
const webcamStream = ref<MediaStream | null>(null);
const webcamVideoRef = ref<HTMLVideoElement | null>(null);
const webcamCanvasRef = ref<HTMLCanvasElement | null>(null);
const webcamError = ref("");

function openWebcamModal() {
  showWebcamModal.value = true;
  webcamError.value = "";
  navigator.mediaDevices.getUserMedia({ video: true })
    .then((stream) => {
      webcamStream.value = stream;
      if (webcamVideoRef.value) {
        webcamVideoRef.value.srcObject = stream;
        webcamVideoRef.value.play();
      }
    })
    .catch((err) => {
      webcamError.value = "Could not access webcam: " + err.message;
    });
}

function closeWebcamModal() {
  showWebcamModal.value = false;
  if (webcamStream.value) {
    webcamStream.value.getTracks().forEach((track) => track.stop());
    webcamStream.value = null;
  }
}

function captureWebcamPhoto() {
  if (!webcamVideoRef.value || !webcamCanvasRef.value) return;
  const video = webcamVideoRef.value;
  const canvas = webcamCanvasRef.value;
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext("2d");
  ctx?.drawImage(video, 0, 0, canvas.width, canvas.height);
  canvas.toBlob((blob) => {
    if (blob) {
      const file = new File([blob], `profile_${Date.now()}.jpg`, { type: blob.type });
      if (previewUrl.value.startsWith("blob:")) {
        URL.revokeObjectURL(previewUrl.value);
      }
      selectedPicture.value = file;
      previewUrl.value = URL.createObjectURL(file);
      closeWebcamModal();
    }
  }, "image/jpeg", 0.92);
}

const form: any = reactive<Record<string, any>>({
  // Personnel
  name: "",
  mobile_phone: "",
  email: "",
  location: "",
  date_of_birth: "",
  // Professional
  profession: "",
  company: "",
  experience: "",
  skills: "",
  linkedin_profile: "",
  // Community
  role_in_community: "",
  blood_group: "",
  interests: "",
  short_bio: "",
  // security
  password: "",
  password_confirmation: "",
});

function addSkillFromInput() {
  const v = String(skillInput.value || '').trim();
  if (!v) return;
  if (!skillsArray.value.includes(v)) {
    skillsArray.value.push(v);
  }
  skillInput.value = '';
  showSkillsSuggestions.value = false;
}

function removeSkill(skill: string) {
  skillsArray.value = skillsArray.value.filter((s) => s !== skill);
}

function pickSuggestion(s: string) {
  skillInput.value = s;
  addSkillFromInput();
}

async function saveProfessionalAndNext() {
  // sync skills array into form.skills as comma joined string
  form.skills = skillsArray.value.join(', ');

  await saveSection('professional');
  if (!errorMessage.value) {
    nextStep();
  }
}

// Theme state for inline radio toggle
const { isDark, setDark } = useTheme();

const themeMode = computed({
  get: () => (isDark.value ? "dark" : "light"),
  set: (val: string) => {
    setDark(val === "dark");
  },
});



onMounted(async () => {
  try {
    user.value = await fetchCurrentUser();
    form.name = user.value.name || "";
    form.mobile_phone = user.value.mobile_number || "";
    form.email = user.value.email || "";
    form.location = (user.value as any).location || "";
    form.date_of_birth = (user.value as any).date_of_birth || "";
    previewUrl.value = user.value.profile_picture_url || "";
    // initialize step 2 fields from user
    form.profession = (user.value as any).profession || '';
    form.company = (user.value as any).company || '';
    form.experience = (user.value as any).experience || '';
    form.skills = (user.value as any).skills || '';
    skillsArray.value = (form.skills || '').split(',').map((s: string) => s.trim()).filter(Boolean);
    form.linkedin_profile = (user.value as any).linkedin_profile || '';
        // community fields
        form.role_in_community = (user.value as any).role_in_community || '';
        form.blood_group = (user.value as any).blood_group || '';
        form.interests = (user.value as any).interests || '';
        form.short_bio = (user.value as any).short_bio || '';
        shortBioCount.value = String(form.short_bio || '').length;
  } catch {
    await logout();
    await router.replace("/home");
  } finally {
    loading.value = false;
  }

  // Diagnostic: log the currently active theme on mount
  nextTick(() => {
    // eslint-disable-next-line no-console
    console.debug('[EditProfilePage] theme on mount isDark=', isDark.value);
  });
  // attach global click listener to close custom dropdowns when clicking outside
  document.addEventListener('click', onDocumentClickCloseDropdowns, true);
  window.addEventListener('resize', onWindowResizeOrScroll, { passive: true });
  window.addEventListener('scroll', onWindowResizeOrScroll, { passive: true });
  // compute the steps line edges after DOM paints
  await nextTick();
  computeStepsLineEdges();
});

onBeforeUnmount(() => {
  if (webcamStream.value) {
    webcamStream.value.getTracks().forEach((track) => track.stop());
    webcamStream.value = null;
  }
  if (previewUrl.value.startsWith("blob:")) {
    URL.revokeObjectURL(previewUrl.value);
  }
  document.removeEventListener('click', onDocumentClickCloseDropdowns, true);
  window.removeEventListener('resize', onWindowResizeOrScroll);
  window.removeEventListener('scroll', onWindowResizeOrScroll);
});

function onDocumentClickCloseDropdowns(e: Event) {
  const target = e.target as Node;
  if (professionOpen.value) {
    const el = professionRef.value;
    if (el && !el.contains(target)) professionOpen.value = false;
  }
  if (experienceOpen.value) {
    const el = experienceRef.value;
    if (el && !el.contains(target)) experienceOpen.value = false;
  }
  if (roleOpen.value) {
    const el = roleRef.value;
    if (el && !el.contains(target)) roleOpen.value = false;
  }
  if (bloodOpen.value) {
    const el = bloodRef.value;
    if (el && !el.contains(target)) bloodOpen.value = false;
  }
}

function toggleProfession() {
  professionOpen.value = !professionOpen.value;
  if (professionOpen.value) {
    experienceOpen.value = false;
    void computeProfessionPlacement();
  }
}

async function computeProfessionPlacement() {
  await nextTick();
  const el = professionRef.value?.getBoundingClientRect();
  if (!el) return;
  const spaceBelow = window.innerHeight - el.bottom;
  const spaceAbove = el.top;
  const preferHeight = 220;
  professionPanelUp.value = spaceBelow < preferHeight && spaceAbove > spaceBelow;
}

function pickProfession(opt: string) {
  form.profession = opt;
  professionOpen.value = false;
}

function toggleExperience() {
  experienceOpen.value = !experienceOpen.value;
  if (experienceOpen.value) {
    professionOpen.value = false;
    void computeExperiencePlacement();
  }
}

function toggleRole() {
  roleOpen.value = !roleOpen.value;
  if (roleOpen.value) {
    experienceOpen.value = false;
    professionOpen.value = false;
    bloodOpen.value = false;
    void computeRolePlacement();
  }
}

async function computeRolePlacement() {
  await nextTick();
  const el = roleRef.value?.getBoundingClientRect();
  if (!el) return;
  const spaceBelow = window.innerHeight - el.bottom;
  const spaceAbove = el.top;
  const preferHeight = 220;
  rolePanelUp.value = spaceBelow < preferHeight && spaceAbove > spaceBelow;
}

function pickRole(opt: string) {
  form.role_in_community = opt;
  roleOpen.value = false;
}

function toggleBlood() {
  bloodOpen.value = !bloodOpen.value;
  if (bloodOpen.value) {
    experienceOpen.value = false;
    professionOpen.value = false;
    roleOpen.value = false;
    void computeBloodPlacement();
  }
}

async function computeBloodPlacement() {
  await nextTick();
  const el = bloodRef.value?.getBoundingClientRect();
  if (!el) return;
  const spaceBelow = window.innerHeight - el.bottom;
  const spaceAbove = el.top;
  const preferHeight = 220;
  bloodPanelUp.value = spaceBelow < preferHeight && spaceAbove > spaceBelow;
}

function pickBlood(opt: string) {
  form.blood_group = opt;
  bloodOpen.value = false;
}

function onWindowResizeOrScroll() {
  if (professionOpen.value) void computeProfessionPlacement();
  if (experienceOpen.value) void computeExperiencePlacement();
  if (roleOpen.value) void computeRolePlacement();
  if (bloodOpen.value) void computeBloodPlacement();
  // recompute edges of steps connector
  computeStepsLineEdges();
}

// ensure recompute when the current step changes (layout may change)
// (watch moved below after `currentStep` is declared to avoid TDZ ReferenceError)

function onShortBioInput() {
  shortBioCount.value = String(form.short_bio || '').length;
}

async function computeExperiencePlacement() {
  await nextTick();
  const el = experienceRef.value?.getBoundingClientRect();
  if (!el) return;
  const spaceBelow = window.innerHeight - el.bottom;
  const spaceAbove = el.top;
  const preferHeight = 220;
  experiencePanelUp.value = spaceBelow < preferHeight && spaceAbove > spaceBelow;
}

function pickExperience(opt: string) {
  form.experience = opt;
  experienceOpen.value = false;
}

function onInterestChange(e: Event, it: string) {
  const checked = (e.target as HTMLInputElement).checked;
  const arr = String(form.interests || '').split(',').map((s: string) => s.trim()).filter(Boolean);
  if (checked && !arr.includes(it)) arr.push(it);
  if (!checked) {
    const idx = arr.indexOf(it); if (idx >= 0) arr.splice(idx, 1);
  }
  form.interests = arr.join(', ');
}

function parsePhone(rawPhone: string, countryCode: string): string | null {
  const value = String(rawPhone || "").trim();
  const fallbackCountry = String(countryCode || "PK").toUpperCase();
  const parsed = value.startsWith("+")
    ? parsePhoneNumberFromString(value)
    : parsePhoneNumberFromString(value, { defaultCountry: fallbackCountry as CountryCode });

  if (!parsed?.countryCallingCode || !parsed?.nationalNumber) {
    return null;
  }

  return `+${parsed.countryCallingCode}${parsed.nationalNumber}`;
}

function handlePhoneInput(_phone: string, phoneObject: any): void {
  const nextCountry = phoneObject?.countryCode?.toUpperCase();

  if (nextCountry) {
    phoneCountry.value = nextCountry;
  }
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
function handlePictureChange(event: Event): void {
  const target = event.target;

  if (!(target instanceof HTMLInputElement)) return;

  const file = target.files?.[0] || null;
  if (!file) return;

  // Validate type
  const allowed = ['image/jpeg', 'image/png', 'image/gif'];
  if (!allowed.includes(file.type)) {
    errorMessage.value = 'Invalid file type. Please upload JPG, PNG or GIF.';
    return;
  }

  // Validate size <= 2MB
  const maxSize = 2 * 1024 * 1024;
  if (file.size > maxSize) {
    errorMessage.value = 'File is too large. Max size is 2MB.';
    return;
  }

  // Clear previous preview
  if (previewUrl.value.startsWith('blob:')) {
    try { URL.revokeObjectURL(previewUrl.value); } catch (e) { /* ignore revoke errors */ }
  }

  selectedPicture.value = file;
  previewUrl.value = URL.createObjectURL(file);
  errorMessage.value = '';
}

function openGalleryPicker(): void {
  const input = document.getElementById("edit-gallery-input");

  if (input instanceof HTMLInputElement) {
    input.click();
  }
}

async function captureFromCamera(): Promise<void> {
  const isNative = Capacitor.isNativePlatform();
  errorMessage.value = "";

  try {
    if (isNative) {
      const existingPermission = await Camera.checkPermissions();
      const needsRequest =
        existingPermission.camera !== "granted" ||
        existingPermission.photos === "prompt" ||
        existingPermission.photos === "prompt-with-rationale" ||
        existingPermission.photos === "denied";

      if (needsRequest) {
        const askedPermission = await Camera.requestPermissions({ permissions: ["camera", "photos"] });

        if (askedPermission.camera !== "granted") {
          errorMessage.value = "Camera permission is not granted. Please allow camera access and try again.";
          return;
        }
      }

      const photo = await Camera.getPhoto({
        source: CameraSource.Camera,
        resultType: CameraResultType.Uri,
        quality: 85,
      });

      if (!photo.webPath) {
        return;
      }

      if (previewUrl.value.startsWith("blob:")) {
        URL.revokeObjectURL(previewUrl.value);
      }

      selectedPicture.value = await webPathToFile(photo.webPath, `profile_${Date.now()}.jpg`);
      previewUrl.value = URL.createObjectURL(selectedPicture.value);
    } else if (!isMobileDevice()) {
      // Desktop/laptop: open webcam modal
      openWebcamModal();
      return;
    } else {
      // Browser fallback where native camera plugin is unavailable.
      cameraInput.value?.click?.();
      return;
    }
  } catch {
    if (!isNative) {
      cameraInput.value?.click?.();
      return;
    }
    errorMessage.value = "Could not open native camera. Please allow camera permission and try again.";
  }
}

async function webPathToFile(webPath: string, filename: string): Promise<File> {
  const response = await fetch(webPath);
  const blob = await response.blob();
  return new File([blob], filename, { type: blob.type || "image/jpeg" });
}

async function submitProfile(): Promise<void> {
  saving.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    // Backwards-compat single submit preserves old behavior: submit all fields
    const parsedMobile = parsePhone(form.mobile_phone, phoneCountry.value);

    if (!parsedMobile) {
      errorMessage.value = "Please enter a valid mobile number with country code.";
      return;
    }

    const payload: Record<string, any> = {
      name: form.name.trim(),
      mobile_number: parsedMobile,
      email: form.email.trim() || null,
      location: form.location || undefined,
      date_of_birth: form.date_of_birth || undefined,
      profession: form.profession || undefined,
      company: form.company || undefined,
      experience: form.experience || undefined,
      skills: form.skills || undefined,
      linkedin_profile: form.linkedin_profile || undefined,
      role_in_community: form.role_in_community || undefined,
      blood_group: form.blood_group || undefined,
      interests: form.interests || undefined,
      short_bio: form.short_bio || undefined,
      password: form.password || undefined,
      password_confirmation: form.password_confirmation || undefined,
    };

    user.value = await updateProfile(payload as any);

    if (selectedPicture.value) {
      user.value = await uploadProfilePicture(selectedPicture.value);
      selectedPicture.value = null;
      previewUrl.value = user.value.profile_picture_url || "";
    }

    // sync back
    form.name = user.value.name || "";
    form.mobile_phone = user.value.mobile_number || "";
    form.email = user.value.email || "";
    form.profession = (user.value as any).profession || "";
    form.company = (user.value as any).company || "";
    form.experience = (user.value as any).experience || "";
    form.skills = (user.value as any).skills || "";
    form.linkedin_profile = (user.value as any).linkedin_profile || "";
    form.role_in_community = (user.value as any).role_in_community || "";
    form.blood_group = (user.value as any).blood_group || "";
    form.interests = (user.value as any).interests || "";
    form.location = (user.value as any).location || "";
    form.date_of_birth = (user.value as any).date_of_birth || "";
    form.password = "";
    form.password_confirmation = "";

    successMessage.value = "Profile updated successfully.";
  } catch (error: any) {
    const fallback = "Could not update profile. Please try again.";
    const validationErrors = error?.response?.data?.errors as Record<string, string[]> | undefined;
    const firstValidationError = validationErrors
      ? Object.values(validationErrors)[0]?.[0]
      : null;

    errorMessage.value =
      (firstValidationError as string | null) || error?.response?.data?.message || fallback;
  } finally {
    saving.value = false;
  }
}

// Save a single section: 'personnel' | 'professional' | 'community'
async function saveSection(section: 'personnel' | 'professional' | 'community') {
  saving.value = true;
  errorMessage.value = "";
  successMessage.value = "";

  try {
    const payload: Record<string, any> = {};

    if (section === 'personnel') {
      const parsedMobile = parsePhone(form.mobile_phone, phoneCountry.value);
      if (!parsedMobile) {
        errorMessage.value = 'Please enter a valid mobile number with country code.';
        return;
      }
      payload.name = form.name.trim();
      payload.mobile_number = parsedMobile;
      payload.email = form.email.trim() || null;
      payload.location = form.location || null;
      payload.date_of_birth = form.date_of_birth || null;
    }

    if (section === 'professional') {
      payload.profession = form.profession || null;
      payload.company = form.company || null;
      payload.experience = form.experience || null;
      payload.skills = form.skills || null;
      payload.linkedin_profile = form.linkedin_profile || null;
    }

    if (section === 'community') {
      payload.role_in_community = form.role_in_community || null;
      payload.blood_group = form.blood_group || null;
      payload.interests = form.interests || null;
      payload.short_bio = form.short_bio || null;
    }

    user.value = await updateProfile(payload);

    successMessage.value = 'Section saved.';
  } catch (err: any) {
    errorMessage.value = err?.response?.data?.message || 'Could not save section.';
  } finally {
    saving.value = false;
  }
}

// Multi-step navigation state (4 steps: Basic Info, Professional, Community, Review)
const currentStep = ref<number>(1);

// compute progress width for visual line between step markers
const progressWidth = computed(() => {
  // four steps: center of first marker at 0%, last at 100%
  const stepIndex = Math.max(1, Math.min(4, currentStep.value));
  // map step index to percentage: 1 -> 0%, 2 -> 33%, 3 -> 66%, 4 -> 100%
  const pct = ((stepIndex - 1) / 3) * 100;
  return pct + '%';
});

function goToStep(step: number) {
  // allow navigating to any previous step, or to the immediate next step
  if (step <= currentStep.value || step <= currentStep.value + 1) {
    currentStep.value = step;
  }
}

// refs to measure the steps container and compute the connecting line edges
const stepsRef = ref<HTMLElement | null>(null);
const lineLeft = ref('0px');
const lineRight = ref('0px');

function computeStepsLineEdges() {
  const container = stepsRef.value;
  if (!container) return;
  const steps = Array.from(container.querySelectorAll<HTMLElement>('.step'));
  if (steps.length === 0) return;
  const first = steps[0].getBoundingClientRect();
  const last = steps[steps.length - 1].getBoundingClientRect();
  const containerRect = container.getBoundingClientRect();
  // center X of first relative to container
  const leftPx = first.left - containerRect.left + first.width / 2;
  // compute right gap from container right to center X of last
  const rightPx = containerRect.right - (last.left + last.width / 2);
  lineLeft.value = `${Math.round(leftPx)}px`;
  lineRight.value = `${Math.round(rightPx)}px`;
}

// ensure recompute when the current step changes (layout may change)
watch(currentStep, async () => { await nextTick(); computeStepsLineEdges(); });

function nextStep(): void {
  if (currentStep.value < 4) currentStep.value += 1;
}

function prevStep(): void {
  if (currentStep.value > 1) currentStep.value -= 1;
}

// Save personnel section then advance to next step (used by the prominent Next button)
async function saveAndNext(): Promise<void> {
  // Save section 'personnel' and if success advance
  await saveSection('personnel');
  if (!errorMessage.value) {
    nextStep();
  }
}

// allow this helper to remain even if not directly referenced by the template
// eslint-disable-next-line @typescript-eslint/no-unused-vars
async function finishEditing(): Promise<void> {
  await submitProfile();
  if (!errorMessage.value) {
    successMessage.value = 'Profile submitted successfully.';
    await router.push('/profile');
  }
}

const showPictureMenu = ref(false);

async function openPictureMenu(event: Event) {
  event.preventDefault();

  const penEl = penRef.value || document.getElementById('profile-picture-pen');
  const menuEl = pictureMenuRef.value || document.getElementById('profile-picture-menu');

  showPictureMenu.value = true;
  document.addEventListener('click', closePictureMenuOnOutside, { capture: true });

  // Wait for DOM to update so we can measure menu
  await nextTick();

  const penRect = penEl?.getBoundingClientRect();
  const menuRect = menuEl?.getBoundingClientRect();
  const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);

  const menuWidth = (menuRect && menuRect.width) || 160;
  const menuHeight = (menuRect && menuRect.height) || 48;

  if (penRect) {
    // try to place above the pen
    let left = penRect.left + penRect.width / 2 - menuWidth / 2;
    // keep menu within viewport
    left = Math.max(8, Math.min(left, vw - menuWidth - 8));

    let top = penRect.top - menuHeight - 10;
    // if not enough space above, place below the pen
    if (top < 8) {
      top = penRect.bottom + 10;
      menuBelow.value = true;
    } else {
      menuBelow.value = false;
    }

    menuPosition.value.top = `${top}px`;
    menuPosition.value.left = `${left}px`;
  }
}

function closePictureMenu() {
  showPictureMenu.value = false;
  menuBelow.value = false;
  document.removeEventListener('click', closePictureMenuOnOutside, { capture: true } as any);
}

function closePictureMenuOnOutside(e: Event) {
  const menu = pictureMenuRef.value || document.getElementById('profile-picture-menu');
  const pen = penRef.value || document.getElementById('profile-picture-pen');
  if (menu && !menu.contains(e.target as Node) && pen && !pen.contains(e.target as Node)) {
    closePictureMenu();
  }
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
function handlePictureMenuSelect(action: 'upload' | 'capture') {
  closePictureMenu();
  if (action === 'upload') openGalleryPicker();
  else if (action === 'capture') captureFromCamera();
}

// Prevent ESLint false-positive unused warnings (template references are not always detected by static analysis)
void handlePictureChange;
void handlePictureMenuSelect;
</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Edit Profile" :content-id="pageContentId" />
    <!-- Theme toggle is now a global component included in App.vue -->

    <ion-content class="ion-padding profile-content">
      <div class="profile-card">
        <!-- Theme radio group inside the profile card -->
        <div class="card-theme-radio">
          <label class="card-theme-radio__label">Appearance</label>
          <div class="card-theme-radio__options">
            <label class="card-theme-radio__option">
              <input type="radio" name="theme" value="light" v-model="themeMode" />
              <span class="card-theme-radio__text">Light <span v-if="themeMode === 'light'" class="card-theme-current">(current)</span></span>
            </label>
            <label class="card-theme-radio__option">
              <input type="radio" name="theme" value="dark" v-model="themeMode" />
              <span class="card-theme-radio__text">Dark <span v-if="themeMode === 'dark'" class="card-theme-current">(current)</span></span>
            </label>
          </div>
        </div>
        <!-- Removed top profile picture UI - picture UI is now only present in Step 1 -->

        <h2>Edit Profile</h2>
        <p v-if="successMessage" class="success-text">{{ successMessage }}</p>
        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>

        <div v-if="!loading" class="form-grid">
          <!-- Step indicators -->
            <div class="steps four" ref="stepsRef">
                                          <div class="steps-line" :style="{ left: lineLeft, right: lineRight }">
                                            <div class="steps-line__base"></div>
                                            <div class="steps-line__active" :style="{ width: progressWidth }"></div>
                                          </div>
              <button type="button" class="step" :class="{ active: currentStep === 1, completed: currentStep > 1 }" @click="goToStep(1)">
                <span class="step-num"> <template v-if="currentStep > 1">✓</template> <template v-else>1</template> </span>
                <span class="step-label">Basic Info</span>
              </button>
              <button type="button" class="step" :class="{ active: currentStep === 2, completed: currentStep > 2 }" @click="goToStep(2)">
                <span class="step-num"> <template v-if="currentStep > 2">✓</template> <template v-else>2</template> </span>
                <span class="step-label">Professional</span>
              </button>
              <button type="button" class="step" :class="{ active: currentStep === 3, completed: currentStep > 3 }" @click="goToStep(3)">
                <span class="step-num"> <template v-if="currentStep > 3">✓</template> <template v-else>3</template> </span>
                <span class="step-label">Community</span>
              </button>
              <button type="button" class="step" :class="{ active: currentStep === 4, completed: currentStep > 4 }" @click="goToStep(4)">
                <span class="step-num"> <template v-if="currentStep > 4">✓</template> <template v-else>4</template> </span>
                <span class="step-label">Review</span>
              </button>
            </div>

          <!-- Personnel step -->
          <div v-show="currentStep === 1" class="step-panel">
            <div class="change-photo">
              <div class="avatar-preview" role="button" @click="openPictureMenu($event)" aria-label="Change photo">
                <img v-if="previewUrl" :src="previewUrl" class="profile-picture large" alt="Profile preview" />
                <div v-else class="profile-picture large profile-picture--placeholder">{{ form.name?.charAt(0)?.toUpperCase() || 'U' }}</div>
                <button id="profile-picture-pen" ref="penRef" class="change-photo-badge" @click.stop="openPictureMenu($event)" aria-label="Change photo" type="button">
                  <span class="camera-inner" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none">
                      <path d="M12 8.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z" fill="#fff" />
                      <path d="M20 7h-3.2l-1.6-1.6A1 1 0 0014.9 5H9.1a1 1 0 00-.7.3L6.8 7H4a1 1 0 00-1 1v10a2 2 0 002 2h14a2 2 0 002-2V8a1 1 0 00-1-1z" fill="#fff"/>
                    </svg>
                  </span>
                </button>

                <!-- Hidden file inputs local to Step 1 so the UI only appears here -->
                <input
                  id="edit-camera-input"
                  ref="cameraInput"
                  class="visually-hidden-file-input"
                  type="file"
                  accept="image/*"
                  capture="environment"
                  @change="handlePictureChange"
                />
                <input
                  id="edit-gallery-input"
                  class="visually-hidden-file-input"
                  type="file"
                  accept="image/*"
                  @change="handlePictureChange"
                />

                <!-- floating menu (appears near avatar pen) -->
                <div
                  v-if="showPictureMenu"
                  class="picture-menu"
                  id="profile-picture-menu"
                  ref="pictureMenuRef"
                  :style="{ top: menuPosition.top, left: menuPosition.left }"
                  :class="{ 'picture-menu--below': menuBelow }"
                >
                  <button type="button" class="picture-menu-item" @click="handlePictureMenuSelect('upload')">Upload from Gallery</button>
                  <button type="button" class="picture-menu-item" @click="handlePictureMenuSelect('capture')">Capture from Camera</button>
                </div>
              </div>
                <div class="change-photo-label" @click="openPictureMenu($event)" role="button" style="cursor:pointer">Change Photo<br/><span class="muted">JPG, PNG or GIF. Max size of 2MB.</span></div>
            </div>

            <label class="field-label">Full Name</label>
            <input v-model="form.name" class="native-input input-field" />

            <label class="field-label">Phone Number</label>
            <VueTelInput
              v-model="form.mobile_phone"
              mode="national"
              default-country="PK"
              :auto-default-country="false"
              :dropdown-options="{ showDialCodeInSelection: true, showDialCodeInList: true, showFlags: true, showSearchBox: true }"
              :input-options="{ placeholder: 'Type mobile number', showDialCode: false }"
              @on-input="handlePhoneInput"
              valid-characters-only
            />

            <label class="field-label">Email Address</label>
            <input v-model="form.email" class="native-input input-field" type="email" />

            <label class="field-label">Location</label>
            <select v-model="form.location" class="native-input input-field">
              <option>Lahore, Pakistan</option>
              <option>Islamabad, Pakistan</option>
              <option>Karachi, Pakistan</option>
            </select>

            <label class="field-label">Date of Birth</label>
            <input v-model="form.date_of_birth" class="native-input input-field" type="date" />

            <div class="step-actions step-actions--single">
              <button class="primary-next full-width" @click="saveAndNext">Next</button>
            </div>
          </div>

          <!-- Professional step -->
          <div v-show="currentStep === 2" class="step-panel">
            <label class="field-label">Profession</label>
            <div class="custom-select" ref="professionRef">
              <button type="button" class="input-field select-btn" @click.stop="toggleProfession">
                <span class="select-value">{{ form.profession || 'Select Profession' }}</span>
                <span class="select-chevron">▾</span>
              </button>
              <div v-if="professionOpen" :class="['select-panel', { 'select-panel--up': professionPanelUp }]">
                <button v-for="opt in professionOptions" :key="opt" type="button" class="select-item" :data-selected="form.profession === opt" @click="pickProfession(opt)">{{ opt }}</button>
              </div>
            </div>

            <label class="field-label">Company</label>
            <input v-model="form.company" class="native-input input-field" placeholder="Company name" />

            <label class="field-label">Experience</label>
            <div class="custom-select" ref="experienceRef">
              <button type="button" class="input-field select-btn" @click.stop="toggleExperience">
                <span class="select-value">{{ form.experience || 'Select Experience' }}</span>
                <span class="select-chevron">▾</span>
              </button>
              <div v-if="experienceOpen" :class="['select-panel', { 'select-panel--up': experiencePanelUp }]">
                <button v-for="opt in experienceOptions" :key="opt" type="button" class="select-item" :data-selected="form.experience === opt" @click="pickExperience(opt)">{{ opt }}</button>
              </div>
            </div>

            <label class="field-label">Skills</label>
            <div class="skills-input">
              <div class="skills-chips">
                <button v-for="skill in skillsArray" :key="skill" type="button" class="skill-chip">
                  <span>{{ skill }}</span>
                  <span type="button" class="skill-remove" @click="removeSkill(skill)">×</span>
                </button>
              </div>
              <div class="skills-entry">
                <input ref="skillsInputRef" v-model="skillInput" @focus="showSkillsSuggestions = true" @keydown.enter.prevent="addSkillFromInput" placeholder="Add skill" class="native-input input-field" />
                <div v-if="showSkillsSuggestions && skillInput.trim().length > 0" class="skills-suggestions">
                  <div
                    v-for="s in skillSuggestions.filter(x => x.toLowerCase().includes(skillInput.toLowerCase()))"
                    :key="s"
                    role="button"
                    tabindex="0"
                    class="suggestion"
                    :data-selected="skillsArray.includes(s)"
                    :aria-disabled="skillsArray.includes(s)"
                    @click="!skillsArray.includes(s) && pickSuggestion(s)"
                    @keydown.enter.prevent="!skillsArray.includes(s) && pickSuggestion(s)"
                  >
                    {{ s }}
                  </div>
                </div>
              </div>
            </div>

            <label class="field-label">LinkedIn Profile (Optional)</label>
            <input v-model="form.linkedin_profile" class="native-input input-field" placeholder="linkedin.com/in/yourprofile" />

            <div class="step-actions">
              <button class="btn btn--outline btn--half" @click="prevStep">Back</button>
              <button class="btn btn--primary btn--half" @click="saveProfessionalAndNext">Next</button>
            </div>
          </div>
          <!-- Community step -->
          <div v-show="currentStep === 3" class="step-panel">
            <label class="field-label">Role in Community</label>
            <div class="custom-select" ref="roleRef" style="margin-bottom:8px;">
              <button type="button" class="input-field select-btn" @click.stop="toggleRole">
                <span class="select-value">{{ form.role_in_community || 'Select role' }}</span>
                <span class="select-chevron">▾</span>
              </button>
              <div v-if="roleOpen" :class="['select-panel', { 'select-panel--up': rolePanelUp }]">
                <button v-for="opt in roleOptions" :key="opt" type="button" class="select-item" :data-selected="form.role_in_community === opt" @click="pickRole(opt)">{{ opt }}</button>
              </div>
            </div>

            <label class="field-label">Blood Group</label>
            <div class="custom-select" ref="bloodRef" style="margin-bottom:8px;">
              <button type="button" class="input-field select-btn" @click.stop="toggleBlood">
                <span class="select-value">{{ form.blood_group || 'Select blood group' }}</span>
                <span class="select-chevron">▾</span>
              </button>
              <div v-if="bloodOpen" :class="['select-panel', { 'select-panel--up': bloodPanelUp }]">
                <button v-for="opt in bloodOptions" :key="opt" type="button" class="select-item" :data-selected="form.blood_group === opt" @click="pickBlood(opt)">{{ opt }}</button>
              </div>
            </div>

            <label class="field-label">Interests</label>
            <div class="interests-list">
              <label v-for="it in interestsList" :key="it" class="interest-item">
                <input type="checkbox" :value="it" :checked="String(form.interests || '').split(',').map(s=>s.trim()).includes(it)" @change="(e) => onInterestChange(e, it)" />
                <span>{{ it }}</span>
              </label>
            </div>

            <label class="field-label">Short Bio (Optional)</label>
            <textarea v-model="form.short_bio" @input="onShortBioInput" class="input-field textarea-field" :maxlength="shortBioMax" rows="4" placeholder="Passionate about community welfare..."></textarea>
            <div class="char-count">{{ shortBioCount }}/{{ shortBioMax }}</div>

            <div class="step-actions">
              <button class="btn btn--outline btn--half" @click="prevStep">Back</button>
              <button class="btn btn--primary btn--half" @click="saveSection('community')">Next</button>
            </div>
          </div>
        </div>

        <p v-else>Loading profile...</p>
      </div>

      <!-- Webcam modal (optional, for desktop/laptop) -->
      <ion-modal :is-open="showWebcamModal" class="webcam-modal">
        <div class="webcam-modal__content">
          <h2>Webcam Capture</h2>
          <div class="webcam-container">
            <video ref="webcamVideoRef" class="webcam-video" autoplay playsinline></video>
            <canvas ref="webcamCanvasRef" class="visually-hidden"></canvas>
            <div v-if="webcamError" class="webcam-error">{{ webcamError }}</div>
          </div>
          <div class="webcam-modal__actions">
            <ion-button @click="captureWebcamPhoto">Capture Photo</ion-button>
            <ion-button @click="closeWebcamModal" color="medium">Close</ion-button>
          </div>
        </div>
      </ion-modal>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.profile-content {
  --padding-bottom: calc(84px + env(safe-area-inset-bottom));
}

.profile-card {
  max-width: 560px;
  margin: 12px auto;
  background: var(--app-surface-color);
  position: relative; /* allow absolute children like card-theme-toggle */
  border-radius: var(--app-card-radius);
  padding: 16px;
  box-shadow: 0 8px 24px rgba(var(--ion-text-color-rgb), 0.1);
}


/* Theme radio group inside card */
.card-theme-radio {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 8px;
  padding-top: 6px;
  border-top: 1px solid var(--app-muted-border-color);
}
.card-theme-radio__label {
  font-weight: 600;
  font-size: 14px;
}
.card-theme-radio__options {
  display: flex;
  gap: 10px;
}
.card-theme-radio__option {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}
.card-theme-radio__option input[type="radio"] {
  width: 16px;
  height: 16px;
}
.card-theme-radio__text {
  font-size: 14px;
}
.card-theme-current {
  font-size: 12px;
  color: var(--app-muted-text-color);
  margin-left: 6px;
}

.form-grid {
  display: grid;
  gap: 10px;
  margin-top: 12px;
}

/* Replace the visually-hidden file input rules with a robust off-screen hide so the browser "Choose file" UI never appears;
   keep inputs programmatically clickable across browsers. */
.visually-hidden-file-input {
  position: absolute !important;
  left: -9999px !important;
  top: -9999px !important;
  width: 1px !important;
  height: 1px !important;
  overflow: hidden !important;
  opacity: 0 !important;
  pointer-events: none !important;
  border: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* Make avatar section match reference: slightly larger, centered and with subtle top spacing */
.profile-picture-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin: 18px 0 18px; /* increased top margin so avatar sits comfortably */
}

.avatar-bg {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(180deg, rgba(226,244,252,1) 0%, rgba(233,247,251,1) 100%);
  box-shadow: 0 8px 24px rgba(25, 50, 80, 0.06);
  position: relative;
}

.profile-picture {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  object-fit: cover;
  border: 6px solid rgba(255,255,255,0.95);
  box-shadow: 0 8px 20px rgba(16,24,40,0.06);
}

.profile-picture--placeholder {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 30px;
  color: var(--ion-color-primary);
  background: transparent;
  border: 6px solid rgba(255,255,255,0.95);
}

/* pen icon small and overlapping bottom-right */
.profile-picture-pen {
  position: absolute;
  right: 10px;
  bottom: 10px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid rgba(0,0,0,0.06);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 22px rgba(16,24,40,0.10);
  z-index: 30;
  cursor: pointer;
}
.profile-picture-pen svg {
  width: 18px;
  height: 18px;
}

/* profile text */
.profile-info {
  text-align: center;
}
.profile-name {
  font-weight: 700;
  font-size: 16px;
  color: var(--ion-text-color);
}
.profile-sub {
  margin-top: 4px;
  font-size: 13px;
  color: var(--app-muted-text-color);
}

/* ensure menu sits above avatar and positioned fixed near pen */
.picture-menu {
  display: flex;
  flex-direction: column;
  min-width: 160px; /* slightly narrower to align with pen */
  padding: 6px 0;
  position: fixed; /* fixed so we can place it using viewport coordinates set from script */
  background: var(--app-surface-color);
  border-radius: 10px;
  box-shadow: 0 8px 28px rgba(16,24,40,0.12);
  z-index: 1200; /* high so it stays above everything */
}

.picture-menu--below {
  /* flip caret and move it above the menu */
  margin-top: -8px;
  padding-top: 8px;
}

/* small caret pointing to the pen */
.picture-menu::after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: calc(50% - 8px); /* center caret under the menu */
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 8px solid var(--app-surface-color);
  filter: drop-shadow(0 2px 6px rgba(16,24,40,0.08));
}

/* when menu is below the pen, flip the caret to point upwards */
.picture-menu--below::after {
  bottom: auto;
  top: -8px;
  left: calc(50% - 8px);
  border-top: none;
  border-bottom: 8px solid var(--app-surface-color);
}

.picture-menu-item {
  background: none;
  border: none;
  text-align: left;
  padding: 10px 16px; /* reduced horizontal padding for smaller width */
  font-size: 15px;
  color: var(--ion-text-color);
  cursor: pointer;
  transition: background 0.15s;
}
.picture-menu-item:hover {
  background: rgba(0,0,0,0.04);
}

.phone-item {
  position: relative;
  overflow: visible;
  z-index: 20;
  --border-color: transparent;
  --border-width: 0;
  --inner-border-color: transparent;
  --inner-border-width: 0;
  --inner-box-shadow: none;
  --highlight-height: 0;
}

.phone-item::part(native) {
  overflow: visible;
  border: 0 !important;
  box-shadow: none !important;
}

:deep(.vti) {
  width: 100%;
  border: 0 !important;
  box-shadow: none !important;
  background: transparent !important;
  outline: none !important;
}

:deep(.vti__input) {
  flex: 1;
  min-width: 0;
  border: 0 !important;
  background: transparent !important;
  color: var(--ion-text-color) !important;
  font-size: 16px;
  padding: 10px 0 8px !important;
}

:deep(.vti__dropdown) {
  background: transparent !important;
  border: 0 !important;
}

:deep(.vti__selection) {
  border: 0 !important;
  box-shadow: none !important;
}

:deep(.vti__dropdown-list) {
  border: 1px solid var(--app-muted-border-color);
  border-radius: 10px;
  background: var(--app-surface-color);
  box-shadow: 0 12px 28px rgba(var(--ion-text-color-rgb), 0.18);
  z-index: 2147483647 !important;
}

:deep(.vti__search_box) {
  border: 1px solid var(--app-muted-border-color) !important;
  border-radius: 8px;
}

.theme-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 0;
  border-top: 1px solid var(--app-muted-border-color);
  margin-top: 8px;
}

.theme-row__label {
  display: flex;
  align-items: center;
  gap: 12px;
}

.theme-row__icon {
  font-size: 24px;
}

.theme-row__title {
  margin: 0;
  font-weight: 600;
  color: var(--ion-text-color);
  font-size: 15px;
}

.theme-row__sub {
  margin: 2px 0 0;
  font-size: 12px;
  color: var(--app-muted-text-color);
}

.theme-toggle-btn {
  width: 52px;
  height: 28px;
  border-radius: 999px;
  background: var(--app-muted-border-color);
  border: none;
  position: relative;
  cursor: pointer;
  transition: background 0.25s;
  flex-shrink: 0;
}

.theme-toggle-btn--active {
  background: var(--ion-color-primary);
}

.theme-toggle-knob {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: #fff;
  transition: transform 0.25s;
  display: block;
}

.theme-toggle-btn--active .theme-toggle-knob {
  transform: translateX(24px);
}

/* Small floating theme toggle at top-right */
.floating-theme-toggle {
  position: fixed;
  top: calc(8px + env(safe-area-inset-top, 0px));
  right: calc(8px + env(safe-area-inset-right, 0px));
  width: 36px;
  height: 36px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--app-surface-color);
  border: 1px solid var(--app-muted-border-color);
  box-shadow: 0 6px 18px rgba(16,24,40,0.08);
  z-index: 2147483647; /* ensure it's above other app layers */
  cursor: pointer;
  padding: 0;
  font-size: 14px;
  transform: translateZ(0);
  pointer-events: auto;
}
.floating-theme-toggle--active {
  background: var(--ion-color-primary);
  color: #fff;
  border-color: transparent;
}
.floating-theme-icon {
  line-height: 1;
  pointer-events: none;
}

@media (max-width: 420px) {
  .floating-theme-toggle {
    top: 6px;
    right: 6px;
    width: 32px;
    height: 32px;
    font-size: 12px;
  }
}

/* Webcam modal styles (optional, for desktop/laptop) */
.webcam-modal {
  --width: 90%;
  --max-width: 400px;
}

.webcam-modal__content {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px;
}

.webcam-container {
  position: relative;
  width: 100%;
  padding-top: 75%; /* 4:3 aspect ratio */
  margin-bottom: 12px;
}

.webcam-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}

.webcam-error {
  color: var(--ion-color-danger);
  margin-top: 8px;
  text-align: center;
}

.webcam-modal__actions {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.steps { display:flex; gap:8px; margin-bottom:12px; }
.steps.four { display:flex; gap:12px; justify-content:flex-start; align-items:center; }
.step { display:flex; align-items:center; gap:8px; padding:6px 8px; border-radius:8px; background:transparent; border:none; cursor:pointer; }
.step .step-num { width:28px; height:28px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; border:1px solid #cfece1; background:#fff; color:#0b6b3a; font-weight:700; }
.step .step-num { width:28px; height:28px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; border:1px solid #cfece1; background:#fff; color:#0b6b3a; font-weight:700; }
.step .step-label { font-size:12px; color:#666; margin-left:6px; }
.step.active .step-num { background:#0b6b3a; color:#fff; border-color:transparent; }
.step.active .step-label { color:#0b6b3a; font-weight:700; }
.step.completed .step-num { background: var(--ion-color-primary, #0b6b3a); color: #fff; border-color: transparent; }
.step.completed .step-label { color: var(--ion-color-primary, #0b6b3a); font-weight:700; }

/* Steps connecting line */
.steps { position: relative; display:flex; gap:12px; justify-content:flex-start; align-items:center; }
.steps {
  --step-size: 36px; /* diameter of step marker; used to align the connector line */
  position: relative;
  display:flex;
  gap:12px;
  justify-content:flex-start;
  align-items:center;
  padding: 0 12px;
}
.steps-line { position:absolute; left: calc(var(--step-size) / 2 + 12px); right: calc(var(--step-size) / 2 + 12px); height:2px; pointer-events:none; top: calc(var(--step-size) / 2 + 2px); z-index:0; }
.steps-line__base { position:absolute; left:0; right:0; height:2px; background:#e9ecec; border-radius:2px; z-index:0; }
.steps-line__active { position:absolute; left:0; height:2px; background: var(--ion-color-primary, #0b6b3a); border-radius:2px; transition: width 220ms ease; z-index:0; }

.step { position: relative; z-index:10; pointer-events:auto; }

/* Make step marker size driven by variable to ensure line aligns to centers */
.step .step-num { width: var(--step-size); height: var(--step-size); }

@media (max-width:420px) {
  .steps-line { left: calc(var(--step-size) / 2 + 12px); right: calc(var(--step-size) / 2 + 12px); }
  .steps { --step-size: 28px; }
}

@media (max-width:420px) {
  .steps-line { left:22px; right:22px; }
}
.step-panel { margin-top:8px; }
.field-label { display:block; margin:8px 0 6px; color:var(--app-muted-text-color); }
.step-actions { display:flex; gap:8px; margin-top:12px; }
.btn { padding:10px 14px; border-radius:10px; border:1px solid rgba(0,0,0,0.06); background:transparent; cursor:pointer; height:48px; display:inline-flex; align-items:center; justify-content:center; }

/* Primary filled button (solid green) */
.btn--primary { background: var(--ion-color-primary, #0b6b3a); color:#fff; border: 1px solid transparent; box-shadow: 0 6px 18px rgba(11,107,58,0.12); font-weight:700; }
.btn--primary:hover { filter: brightness(0.96); }

/* Outlined variant matching primary green */
.btn--outline { background: transparent; color: var(--ion-color-primary, #0b6b3a); border: 2px solid var(--ion-color-primary, #0b6b3a); font-weight:700; }
.btn--outline:hover { background: rgba(11,107,58,0.04); }

/* Half width buttons for side-by-side layout on larger screens */
.btn--half { width:48%; padding:12px 16px; border-radius:10px; }

/* Step-actions: side-by-side on desktop, stacked on small screens */
.step-actions { display:flex; gap:12px; margin-top:12px; align-items:center; }
.step-actions--single { display:flex; }

@media (max-width:420px) {
  .btn--half { width:100%; }
  .step-actions { flex-direction:column; }
}

.primary-next { background:#0b6b3a; color:#fff; border-color:transparent; padding:12px 18px; border-radius:10px; }

.change-photo { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
.avatar-preview { position:relative; }
.profile-picture.large { width:120px; height:120px; border-radius:999px; border:6px solid rgba(255,255,255,0.95); }
.change-photo-btn { position:absolute; right:4px; bottom:4px; background:#0b6b3a; color:#fff; border-radius:999px; width:40px; height:40px; border:none; cursor:pointer; }
.change-photo-label .muted { font-size:12px; color:var(--app-muted-text-color); }

/* New design: centered step indicators and rounded input fields */
.steps.four { display:flex; gap:20px; justify-content:center; align-items:center; margin-bottom:20px; }
.step { display:flex; flex-direction:column; align-items:center; gap:6px; padding:4px 6px; border-radius:8px; background:transparent; border:none; cursor:pointer; }
.step .step-num { width:36px; height:36px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; border:2px solid #e6efe9; background:#fff; color:#0b6b3a; font-weight:700; }
.step .step-label { font-size:12px; color:#8aa79a; }
.step.active .step-num { background:#0b6b3a; color:#fff; border-color:transparent; }
.step.active .step-label { color:#0b6b3a; font-weight:700; }

.input-field { display:block; width:100%; padding:12px 14px; border-radius:12px; border:1px solid #e6e6e6; background:#fff; box-shadow:none; height:48px; line-height:24px; }
.input-field:focus { outline:none; border-color:#d0e9df; box-shadow:0 6px 20px rgba(11,107,58,0.06); }

/* Styled select to match card-like dropdown in reference */
select.input-field {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  padding-right: 44px; /* space for chevron */
  background-color: #fff;
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 20px 20px;
  cursor: pointer;
}

/* Inline SVG chevron (dark green) */
select.input-field {
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24'><path fill='%230b6b3a' d='M7 10l5 5 5-5z'/></svg>");
}

/* Ensure the select looks like the other inputs on small screens */
select.input-field:focus { background-color: #fff; }

/* Suggestions dropdown styling aligned with card look */
.skills-suggestions { position:absolute; top:44px; left:0; right:0; background:#fff; border:1px solid #e6efe9; border-radius:10px; padding:8px; display:flex; gap:6px; flex-wrap:wrap; z-index:1200; box-shadow: 0 8px 22px rgba(16,24,40,0.08); }
.suggestion {
  background:#fff;
  border:1px solid #e6efe9;
  padding:6px 10px;
  border-radius:8px;
  cursor:pointer;
  color: var(--ion-text-color);
  font-size:14px;
  line-height:1;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:6px;
  font-weight:600;
}
/* selected suggestions are green and disabled */
.suggestion[data-selected='true'] {
  color: #fff;
  background: var(--ion-color-primary, #0b6b3a);
  border-color: var(--ion-color-primary, #0b6b3a);
  opacity: 0.95;
  pointer-events: none;
}
.suggestion[aria-disabled='true'] { pointer-events: none; opacity: 0.9; }
.suggestion:focus { outline: 2px solid rgba(11,107,58,0.14); outline-offset: 2px; }

/* skill chip text explicit color and remove button */
.skill-chip { display:inline-flex; align-items:center; gap:8px; background:#f3f7f4; border-radius:999px; padding:6px 10px; border:1px solid #e6efe9; cursor:default; }
.skill-chip span { font-size:13px; color: var(--ion-color-primary, #0b6b3a); }
.skill-remove { margin-left:6px; background:transparent; border:none; font-size:14px; line-height:1; cursor:pointer; color: var(--ion-text-color); }

/* Custom select styles */
.custom-select { position:relative; }
.select-btn { display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; background:#fff; border-radius:12px; border:1px solid #e6e6e6; padding:12px 14px; height:48px; cursor:pointer; }
.select-value { color: var(--ion-color-primary, #0b6b3a); }
.select-chevron { color: var(--ion-color-primary, #0b6b3a); font-size:16px; }
.select-panel { position:absolute; left:0; right:0; top:56px; background:#fff; border:1px solid rgba(11,107,58,0.08); border-radius:10px; box-shadow:0 12px 30px rgba(16,24,40,0.12); z-index:1300; padding:6px; display:flex; flex-direction:column; box-sizing:border-box; overflow:hidden; }
.select-item { background:transparent; border:none; text-align:left; padding:10px 12px; border-radius:8px; cursor:pointer; font-size:15px; color:var(--ion-text-color); }
.select-item:hover { background: rgba(11,107,58,0.06); }

@media (max-width:420px) {
  .select-panel { max-height:240px; overflow:auto; top:54px; }
}

.select-panel--up { top: auto; bottom: 56px; }
.select-panel { max-height: 320px; overflow:auto; }
.select-item[data-selected='true'] { background: var(--ion-color-primary, #0b6b3a); color:#fff; }
.select-item:focus { outline:none; background: rgba(11,107,58,0.06); }

/* prevent panel from overflowing horizontally */
.select-panel, .select-panel * { max-width: 100%; overflow-x: hidden; }

/* For small viewports ensure select panel fits inside card and uses scroll when needed */
@media (max-width:420px) {
  .select-panel { max-height:240px; overflow:auto; top:54px; }
}

/* Interests checklist */
.interests-list { display:flex; flex-direction:column; gap:8px; margin:8px 0 12px; }
.interest-item { display:flex; align-items:center; gap:8px; font-size:14px; }
.interest-item input[type="checkbox"] { width:18px; height:18px; accent-color: var(--ion-color-primary); }

/* Textarea styles and character count */
.textarea-field { min-height:96px; resize:vertical; padding:12px; border-radius:10px; }
.char-count { text-align:right; font-size:12px; color:var(--app-muted-text-color); margin-top:6px; }

.step-actions--single { margin-top:20px; }
.full-width { width:100%; padding:14px 18px; border-radius:10px; background:#0b6b3a; color:#fff; border:none; font-weight:600; }

/* Skills chips and suggestions */
.skills-input { display:flex; flex-direction:column; gap:8px; }
.skills-chips { display:flex; flex-wrap:wrap; gap:8px; }
.skill-chip { display:inline-flex; align-items:center; gap:8px; background:#f3f7f4; border-radius:999px; padding:6px 10px; border:1px solid #e6efe9; cursor:default; }
.skill-chip span { font-size:13px; color:#0b6b3a; }
.skill-remove { margin-left:6px; background:transparent; border:none; font-size:14px; line-height:1; cursor:pointer; }
.skills-entry { position:relative; }
.skills-suggestions { position:absolute; top:44px; left:0; right:0; background:var(--app-surface-color); border:1px solid var(--app-muted-border-color); border-radius:10px; padding:8px; display:flex; gap:6px; flex-wrap:wrap; z-index:1200; }
.suggestion { background:#fff; border:1px solid #e6efe9; padding:6px 10px; border-radius:8px; cursor:pointer; }
</style>



