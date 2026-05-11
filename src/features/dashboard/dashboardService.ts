import { DashboardStat, DashboardEvent, DashboardAnnouncement } from "@/types/dashboard";
import { peopleOutline, peopleCircleOutline, calendarOutline, folderOutline } from 'ionicons/icons';

export function useDashboardData() {
  // In a real app, fetch from API
  const stats: DashboardStat[] = [
    { title: "Total Members", value: "2,458", trend: "+12% this month", icon: peopleOutline },
    { title: "Active Groups", value: "24", trend: "+3 new groups", icon: peopleCircleOutline },
    { title: "Upcoming Events", value: "7", trend: "View all events", icon: calendarOutline },
    { title: "Projects", value: "15", trend: "In progress", icon: folderOutline },
  ];
  const event: DashboardEvent = {
    title: "Upcoming Event",
    date: "May 25, 2024",
    location: "Lahore, Pakistan",
    image: "/src/assets/castle.png",
    cta: "Log in to register"
  };
  const announcements: DashboardAnnouncement[] = [
    { title: "Annual Gathering 2024", icon: "megaphone-outline", color: "yellow", date: "10-May-2026", desc: "Join us for the biggest Khanzada gathering. May 20, 2024" },
    { title: "Blood Donation Camp", icon: "water-outline", color: "red", date: "10-May-2026", desc: "Be a hero, donate blood and save lives. May 10, 2024" },
    { title: "Education Support Program", icon: "school-outline", color: "green", date: "10-May-2026", desc: "Be a hero, donate blood and save lives. May 10, 2024" },
  ];
  return { stats, event, announcements };
}

