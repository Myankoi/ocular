import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';
import {
    ArrowLeft,
    ArrowRight,
    Archive,
    BookOpen,
    CalendarClock,
    CalendarDays,
    Camera,
    CameraOff,
    Check,
    ClipboardList,
    Clock3,
    Download,
    FileSpreadsheet,
    GraduationCap,
    Home,
    LogOut,
    MapPin,
    Maximize2,
    Menu,
    Minimize2,
    Search,
    School,
    ScanLine,
    SlidersHorizontal,
    SwitchCamera,
    Upload,
    UsersRound,
    X,
    createIcons,
} from 'lucide';

const lucideIcons = {
    ArrowLeft,
    ArrowRight,
    Archive,
    BookOpen,
    CalendarClock,
    CalendarDays,
    Camera,
    CameraOff,
    Check,
    ClipboardList,
    Clock3,
    Download,
    FileSpreadsheet,
    GraduationCap,
    Home,
    LogOut,
    MapPin,
    Maximize2,
    Menu,
    Minimize2,
    Search,
    School,
    ScanLine,
    SlidersHorizontal,
    SwitchCamera,
    Upload,
    UsersRound,
    X,
};

window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeSupportedFormats = Html5QrcodeSupportedFormats;
window.refreshLucideIcons = () => createIcons({
    icons: {
        ...lucideIcons,
    },
    attrs: {
        'stroke-width': 2.4,
    },
});

window.refreshLucideIcons();
