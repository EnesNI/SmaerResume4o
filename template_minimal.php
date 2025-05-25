<?php
// Minimal Clean Template
$template_data = $_GET['data'] ?? [];
$preview_mode = isset($_GET['preview']);

// Sample data for preview
if ($preview_mode) {
    $template_data = [
        'full_name' => 'Michael Chen',
        'email' => 'michael.chen@email.com',
        'phone' => '+1 (555) 456-7890',
        'location' => 'Seattle, WA',
        'linkedin' => 'linkedin.com/in/michaelchen',
        'summary' => 'Results-driven Product Manager with 6+ years of experience leading cross-functional teams to deliver innovative digital products. Expertise in agile methodologies, user research, and data-driven decision making.',
        'experience' => [
            [
                'title' => 'Senior Product Manager',
                'company' => 'Innovation Labs',
                'duration' => '2021 - Present',
                'description' => 'Led product strategy for B2B SaaS platform serving 10,000+ customers. Increased user retention by 45% through feature optimization and user experience improvements.'
            ],
            [
                'title' => 'Product Manager',
                'company' => 'TechCorp',
                'duration' => '2019 - 2021',
                'description' => 'Managed product roadmap for mobile application with 500K+ downloads. Collaborated with engineering and design teams to deliver features on time and within budget.'
            ]
        ],
        'education' => [
            [
                'degree' => 'Master of Business Administration',
                'school' => 'Stanford Graduate School of Business',
                'year' => '2019'
            ],
            [
                'degree' => 'Bachelor of Science in Engineering',
                'school' => 'University of Washington',
                'year' => '2017'
            ]
        ],
        'skills' => ['Product Strategy', 'Agile/Scrum', 'Data Analysis', 'User Research', 'A/B Testing', 'SQL', 'Figma', 'Jira']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimal Clean Resume</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #2c3e50;
            background: #ffffff;
        }

        .resume-container {
            max-width: 750px;
            margin: 3rem auto;
            background: white;
            padding: 3rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #ecf0f1;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #ecf0f1;
        }

        .name {
            font-size: 2.5rem;
            font-weight: 400;
            color: #2c3e50;
            margin-bottom: 1rem;
            letter-spacing: 2px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2rem;
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #2c3e50;
        }

        .summary {
            font-size: 1rem;
            line-height: 1.8;
            color: #34495e;
            text-align: justify;
        }

        .experience-item, .education-item {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #ecf0f1;
        }

        .experience-item:last-child, .education-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .job-header, .education-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .job-title, .degree {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .company, .school {
            font-style: italic;
            color: #7f8c8d;
            margin-top: 0.25rem;
        }

        .duration, .year {
            font-size: 0.9rem;
            color: #95a5a6;
            font-weight: 500;
        }

        .description {
            color: #34495e;
            line-height: 1.7;
            margin-top: 0.75rem;
        }

        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .skill-item {
            background: #ecf0f1;
            color: #2c3e50;
            padding: 0.5rem 1rem;
            border-radius: 3px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media print {
            .resume-container {
                box-shadow: none;
                margin: 0;
                padding: 2rem;
                border: none;
            }
        }

        @media (max-width: 768px) {
            .resume-container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .name {
                font-size: 2rem;
            }
            
            .contact-info {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .job-header, .education-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="header">
            <h1 class="name"><?php echo htmlspecialchars($template_data['full_name'] ?? 'Your Name'); ?></h1>
            <div class="contact-info">
                <?php if (!empty($template_data['email'])): ?>
                    <div class="contact-item"><?php echo htmlspecialchars($template_data['email']); ?></div>
                <?php endif; ?>
                <?php if (!empty($template_data['phone'])): ?>
                    <div class="contact-item"><?php echo htmlspecialchars($template_data['phone']); ?></div>
                <?php endif; ?>
                <?php if (!empty($template_data['location'])): ?>
                    <div class="contact-item"><?php echo htmlspecialchars($template_data['location']); ?></div>
                <?php endif; ?>
                <?php if (!empty($template_data['linkedin'])): ?>
                    <div class="contact-item"><?php echo htmlspecialchars($template_data['linkedin']); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($template_data['summary'])): ?>
        <div class="section">
            <h2 class="section-title">Summary</h2>
            <p class="summary"><?php echo htmlspecialchars($template_data['summary']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($template_data['experience'])): ?>
        <div class="section">
            <h2 class="section-title">Experience</h2>
            <?php foreach ($template_data['experience'] as $job): ?>
            <div class="experience-item">
                <div class="job-header">
                    <div>
                        <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                        <div class="company"><?php echo htmlspecialchars($job['company']); ?></div>
                    </div>
                    <div class="duration"><?php echo htmlspecialchars($job['duration']); ?></div>
                </div>
                <div class="description"><?php echo htmlspecialchars($job['description']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($template_data['education'])): ?>
        <div class="section">
            <h2 class="section-title">Education</h2>
            <?php foreach ($template_data['education'] as $edu): ?>
            <div class="education-item">
                <div class="education-header">
                    <div>
                        <div class="degree"><?php echo htmlspecialchars($edu['degree']); ?></div>
                        <div class="school"><?php echo htmlspecialchars($edu['school']); ?></div>
                    </div>
                    <div class="year"><?php echo htmlspecialchars($edu['year']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($template_data['skills'])): ?>
        <div class="section">
            <h2 class="section-title">Skills</h2>
            <div class="skills-list">
                <?php foreach ($template_data['skills'] as $skill): ?>
                <div class="skill-item"><?php echo htmlspecialchars($skill); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
