<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Demo content for frontend work. Uses firstOrCreate so existing rows are never touched
 * and re-running does not create duplicates.
 */
class PortfolioDemoSeeder extends Seeder
{
    /**
     * Images that already exist in public/uploads, reused for demo records.
     *
     * @var array<int, string>
     */
    private array $projectImages = [
        'uploads/project/85l4siUJwrpkahTu1D5zwdDhcm1kOTQEnVAAXoUr.png',
        'uploads/project/8zluX2iFSchWjtGnBhI6lTozYrppWfhsppL6AzCQ.jpg',
        'uploads/project/aaXpezWfeZIutWtGo5rR6EGDCJtTHVwKt5KpXdNY.jpg',
        'uploads/project/ffVXT1Ii8N7DgKJtwfoxPNOBFUDab4seywHNgm5d.jpg',
        'uploads/project/fmhcnZ6E5NGXR2ttUUCJmAG3bRwt9a5MqZ4xdF2c.png',
        'uploads/project/ftA0B98aAfXOyjDfW7sZiEZdnt8ztQVUdCuRet8G.jpg',
        'uploads/project/krJ3KIiISS0rAeU1RULmY7VDNJ90WdJ0VrTsCtkR.jpg',
        'uploads/project/Mmx7pIfHbFjm4VXtXXxhEy8v4FtFlPrPGfrFQtEc.jpg',
        'uploads/project/NBXefLZQS3a312Rku1IEznZAUWzcYoJMeucqfQqH.jpg',
        'uploads/project/NuQsDao2tKenIqV01HRjzdJt7AD84jbOotLOy1Ah.jpg',
        'uploads/project/UIVG9l398sJRACiwzJvyUpOJe28Qo1BYyy7z3aYn.jpg',
        'uploads/project/vJRtG5x2xEE2KvXK9eyKsAGOtKdHboyM4mMXTFlr.jpg',
    ];

    public function run(): void
    {
        Model::unguarded(function (): void {
            $this->seedSkills();
            $this->seedServices();
            $this->seedProjects();
            $this->seedBlogPosts();
            $this->seedExperiences();
            $this->seedEducation();
            $this->seedTraining();
            $this->seedCertifications();
            $this->seedTestimonials();
            $this->seedTeams();
        });
    }

    private function image(int $index): string
    {
        return $this->projectImages[$index % count($this->projectImages)];
    }

    private function categoryId(string $slug): ?int
    {
        return Category::where('slug', $slug)->value('id');
    }

    private function seedSkills(): void
    {
        $skills = [
            ['JavaScript', 'frontend', 92, 6, 'fa-brands fa-js', '#F7DF1E'],
            ['Vue.js', 'frontend', 85, 4, 'fa-brands fa-vuejs', '#42B883'],
            ['Next.js', 'frontend', 80, 3, 'fa-solid fa-n', '#000000'],
            ['Node.js', 'backend', 82, 4, 'fa-brands fa-node-js', '#339933'],
            ['REST API Design', 'backend', 90, 5, 'fa-solid fa-plug', '#6366F1'],
            ['PostgreSQL', 'database', 75, 3, 'fa-solid fa-database', '#336791'],
            ['Redis', 'database', 70, 3, 'fa-solid fa-bolt', '#DC382D'],
            ['Docker', 'devops', 78, 3, 'fa-brands fa-docker', '#2496ED'],
            ['AWS', 'devops', 68, 2, 'fa-brands fa-aws', '#FF9900'],
            ['GitHub Actions', 'devops', 74, 3, 'fa-brands fa-github', '#2088FF'],
            ['Dart', 'mobile', 80, 3, 'fa-solid fa-mobile-screen', '#0175C2'],
            ['Git', 'tools', 93, 6, 'fa-brands fa-git-alt', '#F05032'],
            ['Figma', 'tools', 72, 3, 'fa-brands fa-figma', '#F24E1E'],
            ['Team Leadership', 'soft_skills', 85, 4, 'fa-solid fa-people-group', '#10B981'],
            ['Problem Solving', 'soft_skills', 95, 6, 'fa-solid fa-lightbulb', '#F59E0B'],
        ];

        foreach ($skills as $order => [$name, $category, $proficiency, $years, $icon, $color]) {
            Skill::firstOrCreate(['slug' => str($name)->slug()->toString()], [
                'name' => $name,
                'category' => $category,
                'proficiency' => $proficiency,
                'years_experience' => $years,
                'icon' => $icon,
                'color' => $color,
                'description' => "Hands-on experience with {$name} across production projects.",
                'is_primary' => $proficiency >= 85,
                'order' => 20 + $order,
                'is_active' => true,
            ]);
        }
    }

    private function seedServices(): void
    {
        $services = [
            ['Web Application Development', 'fa-solid fa-code', 'Custom, scalable web apps built with Laravel and modern JavaScript.', ['Custom dashboards', 'Role based access', 'Payment integration', 'Real-time features'], ['Laravel', 'Vue.js', 'React', 'MySQL'], 1200, 'project'],
            ['API Development & Integration', 'fa-solid fa-plug-circle-bolt', 'Secure, documented REST APIs and third-party integrations.', ['RESTful design', 'Token authentication', 'Postman docs', 'Rate limiting'], ['Laravel', 'Node.js', 'Postman'], 800, 'project'],
            ['Mobile App Development', 'fa-solid fa-mobile-screen-button', 'Cross-platform mobile apps with a native look and feel.', ['iOS & Android', 'Offline support', 'Push notifications', 'Store publishing'], ['Flutter', 'Dart', 'Firebase'], 1500, 'project'],
            ['UI/UX Design', 'fa-solid fa-pen-ruler', 'Clean, conversion-focused interfaces from wireframe to prototype.', ['Wireframes', 'Interactive prototypes', 'Design systems', 'Usability review'], ['Figma', 'Tailwind CSS'], 500, 'project'],
            ['Performance & SEO Optimization', 'fa-solid fa-gauge-high', 'Faster pages, better Core Web Vitals and higher rankings.', ['Speed audit', 'Query optimization', 'Caching strategy', 'Technical SEO'], ['Redis', 'Lighthouse', 'MySQL'], 300, 'audit'],
            ['Maintenance & Support', 'fa-solid fa-screwdriver-wrench', 'Ongoing updates, monitoring and bug fixes for your app.', ['Security updates', 'Uptime monitoring', 'Backups', 'Priority support'], ['Laravel', 'Docker', 'AWS'], 150, 'month'],
        ];

        foreach ($services as $order => [$title, $icon, $short, $features, $tech, $price, $unit]) {
            Service::firstOrCreate(['slug' => str($title)->slug()->toString()], [
                'title' => $title,
                'short_description' => $short,
                'description' => "{$short}\n\nEvery engagement starts with a discovery call, followed by a clear plan, weekly progress updates and a smooth handover.",
                'icon' => $icon,
                'image' => $this->image($order + 3),
                'features' => $features,
                'technologies' => $tech,
                'starting_price' => $price,
                'price_unit' => $unit,
                'order' => 10 + $order,
                'is_active' => true,
            ]);
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            ['Task Management SaaS', 'full-stack-applications', 'web', 'Kanban-style task manager with teams, real-time updates and reports.', ['Laravel', 'Vue.js', 'Pusher', 'MySQL'], 'Nimbus Labs', true],
            ['Hospital Appointment System', 'full-stack-applications', 'web', 'Online doctor booking with schedules, reminders and patient records.', ['Laravel', 'Livewire', 'Tailwind CSS'], 'CareFirst Clinic', true],
            ['Food Delivery App', 'flutter-apps', 'mobile', 'Cross-platform delivery app with live order tracking and wallet.', ['Flutter', 'Firebase', 'Google Maps'], 'FoodHub BD', true],
            ['Payment Gateway API', 'backend-api-development', 'web', 'Unified API wrapping bKash, Nagad and Stripe with webhooks.', ['Laravel', 'Redis', 'Docker'], 'PayFlow', false],
            ['Real Estate Listing Portal', 'frontend-projects', 'web', 'Property search with map view, filters and saved listings.', ['React', 'Next.js', 'Tailwind CSS'], 'HomeNest', true],
            ['Fitness Tracker PWA', 'progressive-web-app', 'web', 'Offline-first workout tracker with charts and streaks.', ['Vue.js', 'IndexedDB', 'Chart.js'], null, false],
            ['Learning Management System', 'full-stack-applications', 'web', 'Courses, quizzes, certificates and instructor dashboards.', ['Laravel', 'React', 'MySQL', 'AWS S3'], 'EduSpark', true],
            ['Expense Tracker iOS App', 'ios-apps', 'mobile', 'Minimal personal finance app with budgets and insights.', ['Swift', 'SwiftUI', 'Core Data'], null, false],
            ['Ride Sharing Android App', 'android-apps', 'mobile', 'Driver and rider apps with fare estimates and live location.', ['Kotlin', 'Firebase', 'Google Maps'], 'GoRide', false],
            ['Agency Landing Page', 'frontend-projects', 'design', 'Animated marketing site with smooth scroll and micro-interactions.', ['HTML', 'Tailwind CSS', 'GSAP'], 'Pixel Studio', false],
        ];

        foreach ($projects as $index => [$title, $categorySlug, $type, $short, $tech, $client, $featured]) {
            $slug = str($title)->slug()->toString();

            Project::firstOrCreate(['slug' => $slug], [
                'title' => $title,
                'short_description' => $short,
                'description' => "{$short}\n\nThe goal was to deliver a fast, reliable and easy-to-use product. I handled the architecture, development and deployment, working closely with the client through weekly demos.",
                'category_id' => $this->categoryId($categorySlug),
                'featured_image' => $this->image($index),
                'images' => [$this->image($index + 1), $this->image($index + 2), $this->image($index + 3)],
                'type' => $type,
                'status' => 'published',
                'client_name' => $client,
                'project_date' => now()->subMonths($index * 2 + 1)->toDateString(),
                'project_url' => "https://example.com/{$slug}",
                'github_url' => "https://github.com/example/{$slug}",
                'demo_url' => "https://demo.example.com/{$slug}",
                'tech_stack' => $tech,
                'features' => ['Responsive design', 'Secure authentication', 'Admin dashboard', 'Analytics & reports'],
                'challenges' => ['Handling real-time updates at scale', 'Keeping the UI fast on low-end devices'],
                'solutions' => ['Queued jobs with Redis and websockets', 'Lazy loading and optimized assets'],
                'duration_weeks' => 4 + $index * 2,
                'team_size' => (string) (1 + $index % 4),
                'my_role' => $index % 2 ? 'Full Stack Developer' : 'Lead Developer',
                'is_featured' => $featured,
                'order' => 10 + $index,
                'views' => random_int(120, 2400),
                'published_at' => now()->subMonths($index * 2 + 1),
            ]);
        }
    }

    private function seedBlogPosts(): void
    {
        $authorId = User::value('id');

        $posts = [
            ['10 Laravel Tips That Will Save You Hours', 'web-development', ['laravel', 'php', 'tips'], true],
            ['Building Offline-First Apps with PWAs', 'technology', ['pwa', 'javascript', 'offline'], true],
            ['Flutter vs React Native in 2026', 'programming', ['flutter', 'react-native', 'mobile'], false],
            ['Designing Clean REST APIs', 'web-development', ['api', 'rest', 'backend'], true],
            ['Tailwind CSS: From Zero to Production', 'web-development', ['tailwind', 'css', 'frontend'], false],
            ['How I Deploy Laravel Apps with Docker', 'technology', ['docker', 'devops', 'laravel'], false],
            ['Understanding Database Indexing', 'programming', ['mysql', 'database', 'performance'], false],
            ['My Journey from Junior to Senior Developer', 'programming', ['career', 'growth'], true],
        ];

        foreach ($posts as $index => [$title, $categorySlug, $tags, $featured]) {
            $post = BlogPost::firstOrCreate(['slug' => str($title)->slug()->toString()], [
                'title' => $title,
                'excerpt' => "A practical guide on \"{$title}\" with real examples from my projects.",
                'content' => "In this post I share what I learned about {$title}. These are practical lessons from real client work.\n\nMost tutorials stop at the basics. Real projects need more: performance, maintainability and a good developer experience.\n\n\"Write code for humans first, computers second.\"\n\nMy approach:\n- Start simple and measure\n- Automate the boring parts\n- Refactor with tests in place\n\nSmall, consistent improvements add up. Try one idea from this post in your next project.",
                'category_id' => $this->categoryId($categorySlug),
                'author_id' => $authorId,
                'featured_image' => $this->image($index + 5),
                'status' => 'published',
                'tags' => $tags,
                'is_featured' => $featured,
                'allow_comments' => true,
                'views' => random_int(80, 5000),
                'likes' => random_int(5, 400),
                'published_at' => now()->subDays($index * 9 + 2),
            ]);

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['slug' => str($tagName)->slug()->toString()], ['name' => str($tagName)->headline()->toString()]);
                if ($post->wasRecentlyCreated) {
                    $post->tagModels()->syncWithoutDetaching([$tag->id]);
                }
            }
        }
    }

    private function seedExperiences(): void
    {
        $experiences = [
            ['Pixel Craft Studio', 'Junior Web Developer', 'full-time', 'Dhaka, Bangladesh', ['HTML', 'CSS', 'jQuery', 'PHP'], '-6 years', '-4 years'],
            ['Freelance (Upwork & Fiverr)', 'Freelance Full Stack Developer', 'freelance', 'Remote', ['Laravel', 'Vue.js', 'Flutter', 'MySQL'], '-4 years', '-3 years'],
        ];

        foreach ($experiences as $index => [$company, $position, $type, $location, $tech, $start, $end]) {
            Experience::firstOrCreate(['company_name' => $company, 'position' => $position], [
                'employment_type' => $type,
                'location' => $location,
                'description' => "Worked as {$position}, building and shipping web products for clients.",
                'responsibilities' => ['Built responsive interfaces', 'Developed backend features and APIs', 'Collaborated with designers and clients'],
                'achievements' => ['Delivered 20+ projects on time', 'Maintained a 5-star client rating'],
                'technologies' => $tech,
                'start_date' => now()->modify($start)->toDateString(),
                'end_date' => now()->modify($end)->toDateString(),
                'is_current' => false,
                'company_url' => 'https://example.com',
                'order' => 10 + $index,
                'is_active' => true,
            ]);
        }
    }

    private function seedEducation(): void
    {
        Education::firstOrCreate(['institution' => 'Coursera', 'degree' => 'Professional Certificate'], [
            'field_of_study' => 'Full Stack Web Development',
            'description' => 'Online program covering frontend, backend, databases and deployment.',
            'location' => 'Online',
            'start_date' => now()->subYears(3)->toDateString(),
            'end_date' => now()->subYears(2)->toDateString(),
            'is_current' => false,
            'grade' => 'With Honors',
            'activities' => ['Capstone project', 'Peer code reviews'],
            'courses' => ['HTML/CSS', 'JavaScript', 'Node.js', 'Databases'],
            'order' => 10,
            'is_active' => true,
        ]);
    }

    private function seedTraining(): void
    {
        $trainings = [
            ['Laravel Advanced Patterns', 'Laracasts', 'course', 'technical', 'completed', 40, 95, ['Service container', 'Queues', 'Testing'], true],
            ['AWS Cloud Practitioner Bootcamp', 'Amazon Web Services', 'bootcamp', 'technical', 'completed', 60, 88, ['EC2', 'S3', 'IAM'], true],
            ['UI/UX Design Fundamentals', 'Google', 'certification', 'design', 'completed', 35, 92, ['Wireframing', 'Prototyping', 'User research'], false],
            ['Agile Scrum Workshop', 'Scrum Alliance', 'workshop', 'business', 'completed', 16, null, ['Scrum', 'Sprint planning'], false],
            ['Docker & Kubernetes Mastery', 'Udemy', 'course', 'technical', 'in_progress', 30, null, ['Docker', 'Kubernetes', 'CI/CD'], false],
            ['Effective Communication', 'LinkedIn Learning', 'course', 'soft_skills', 'planned', 8, null, ['Presentation', 'Writing'], false],
        ];

        $colors = ['#6366F1', '#F59E0B', '#EC4899', '#10B981', '#2496ED', '#8B5CF6'];

        foreach ($trainings as $index => [$title, $provider, $type, $category, $status, $hours, $score, $skills, $featured]) {
            Training::firstOrCreate(['slug' => str($title)->slug()->toString()], [
                'title' => $title,
                'provider' => $provider,
                'type' => $type,
                'category' => $category,
                'description' => "{$title} by {$provider}.",
                'certificate_url' => $status === 'completed' ? 'https://example.com/certificate' : null,
                'credential_id' => $status === 'completed' ? 'CERT-'.(1000 + $index) : null,
                'start_date' => now()->subMonths(12 - $index * 2)->toDateString(),
                'end_date' => $status === 'completed' ? now()->subMonths(11 - $index * 2)->toDateString() : null,
                'duration_hours' => $hours,
                'status' => $status,
                'score' => $score,
                'skills_gained' => $skills,
                'rating' => $status === 'completed' ? 4.5 : null,
                'icon' => 'fa-solid fa-graduation-cap',
                'color' => $colors[$index],
                'order' => 10 + $index,
                'is_featured' => $featured,
                'is_active' => true,
            ]);
        }
    }

    private function seedCertifications(): void
    {
        $certifications = [
            ['AWS Certified Cloud Practitioner', 'Amazon Web Services', ['AWS', 'Cloud'], true],
            ['Meta Front-End Developer', 'Meta', ['React', 'JavaScript'], false],
            ['Google UX Design Certificate', 'Google', ['Figma', 'UX'], false],
            ['Zend Certified PHP Engineer', 'Zend', ['PHP'], false],
        ];

        foreach ($certifications as $index => [$name, $org, $skills, $expires]) {
            Certification::firstOrCreate(['name' => $name], [
                'issuing_organization' => $org,
                'credential_id' => 'CRED-'.(5000 + $index),
                'credential_url' => 'https://example.com/verify',
                'description' => "Official {$org} certification.",
                'issue_date' => now()->subMonths(6 + $index * 5)->toDateString(),
                'expiry_date' => $expires ? now()->addYears(2)->toDateString() : null,
                'does_expire' => $expires,
                'skills' => $skills,
                'order' => $index,
                'is_active' => true,
            ]);
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['Michael Turner', 'CTO', 'Nimbus Labs', 'Outstanding work on our SaaS platform. Clean code, great communication and delivered ahead of schedule.', 5, 'task-management-saas'],
            ['Nadia Rahman', 'Founder', 'FoodHub BD', 'The app exceeded our expectations. Our customers love the smooth ordering experience.', 5, 'food-delivery-app'],
            ['David Kim', 'Product Manager', 'EduSpark', 'Very professional and proactive. He suggested improvements we had not even thought of.', 5, 'learning-management-system'],
            ['Emily Watson', 'Marketing Lead', 'Pixel Studio', 'Our new landing page doubled our conversion rate. Beautiful animations and super fast.', 4, 'agency-landing-page'],
        ];

        $avatars = ['assets/img/avatars/1.png', 'assets/img/avatars/5.png', 'assets/img/avatars/6.png', 'assets/img/avatars/7.png'];

        foreach ($testimonials as $index => [$name, $position, $company, $content, $rating, $projectSlug]) {
            Testimonial::firstOrCreate(['client_name' => $name], [
                'client_position' => $position,
                'client_company' => $company,
                'content' => $content,
                'rating' => $rating,
                'client_image' => $avatars[$index],
                'client_linkedin' => 'https://linkedin.com',
                'project_id' => Project::where('slug', $projectSlug)->value('id'),
                'is_featured' => $index < 2,
                'is_active' => true,
                'order' => 10 + $index,
            ]);
        }
    }

    private function seedTeams(): void
    {
        $members = [
            ['Tanvir Ahmed', 'UI/UX Designer', 'design', 'contract', ['Figma', 'Adobe XD', 'Prototyping'], '#EC4899'],
            ['Farhana Akter', 'Backend Developer', 'engineering', 'full_time', ['Laravel', 'MySQL', 'Redis'], '#6366F1'],
            ['Rafiq Islam', 'Mobile Developer', 'engineering', 'part_time', ['Flutter', 'Dart', 'Firebase'], '#10B981'],
            ['Sadia Noor', 'Digital Marketer', 'marketing', 'contract', ['SEO', 'Content', 'Ads'], '#F59E0B'],
        ];

        foreach ($members as $index => [$name, $position, $department, $type, $skills, $color]) {
            Team::firstOrCreate(['slug' => str($name)->slug()->toString()], [
                'name' => $name,
                'position' => $position,
                'department' => $department,
                'bio' => "{$name} is a passionate {$position} who loves turning ideas into great products.",
                'email' => str($name)->slug('.')->append('@example.com')->toString(),
                'image' => 'uploads/teams/l7Efd5fDBcfI0s2MLBaRy9U13K1cmYbGeLluGVsk.jpg',
                'linkedin' => 'https://linkedin.com',
                'github' => 'https://github.com',
                'skills' => $skills,
                'join_date' => now()->subMonths(6 + $index * 4)->toDateString(),
                'employment_type' => $type,
                'location' => 'Dhaka, Bangladesh',
                'is_featured' => $index < 2,
                'is_active' => true,
                'order' => 10 + $index,
                'icon' => 'fa-solid fa-user',
                'color' => $color,
            ]);
        }
    }
}
