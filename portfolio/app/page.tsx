import Link from "next/link"
import Image from "next/image"
import { ArrowRight, Github, Linkedin, Mail } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import ProjectCard from "@/components/project-card"
import SkillCard from "@/components/skill-card"
import MobileNav from "@/components/mobile-nav"

export default function Home() {
  return (
    <div className="min-h-screen bg-background">
      {/* Navigation */}
      <header className="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div className="container flex h-16 items-center justify-between">
          <div className="font-bold text-xl">
            <Link href="/">Portfolio</Link>
          </div>
          <nav className="hidden md:flex gap-6">
            <Link href="#home" className="text-sm font-medium hover:text-primary transition-colors">
              Home
            </Link>
            <Link href="#about" className="text-sm font-medium hover:text-primary transition-colors">
              About Me
            </Link>
            <Link href="#projects" className="text-sm font-medium hover:text-primary transition-colors">
              Projects
            </Link>
            <Link href="#skills" className="text-sm font-medium hover:text-primary transition-colors">
              Skills
            </Link>
            <Link href="#contact" className="text-sm font-medium hover:text-primary transition-colors">
              Contact
            </Link>
          </nav>
          <div className="md:hidden">
            <MobileNav />
          </div>
        </div>
      </header>

      <main className="flex flex-col items-center">
        {/* Hero Section */}
        <section id="home" className="container py-12 md:py-24 lg:py-32">
          <div className="flex flex-col md:flex-row items-center gap-8 md:gap-16">
            <div className="flex-1 space-y-4">
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tighter">
                Hi, I'm <span className="text-primary">John Doe</span>
              </h1>
              <p className="text-xl text-muted-foreground">
                A passionate BTech student and aspiring full-stack developer
              </p>
              <div className="flex gap-4">
                <Button asChild>
                  <Link href="#projects">
                    See My Work <ArrowRight className="ml-2 h-4 w-4" />
                  </Link>
                </Button>
                <Button variant="outline" asChild>
                  <Link href="#contact">Contact Me</Link>
                </Button>
              </div>
            </div>
            <div className="flex-1 flex justify-center">
              <div className="relative w-64 h-64 md:w-80 md:h-80 overflow-hidden rounded-full border-4 border-primary">
                <Image
                  src="/placeholder.svg?height=320&width=320"
                  alt="Profile"
                  fill
                  className="object-cover"
                  priority
                />
              </div>
            </div>
          </div>
        </section>

        {/* About Me Section */}
        <section id="about" className="w-full py-12 md:py-24 bg-muted/50">
          <div className="container">
            <h2 className="text-3xl md:text-4xl font-bold text-center mb-12">About Me</h2>
            <div className="flex flex-col md:flex-row gap-8 items-center">
              <div className="md:w-1/3 flex justify-center">
                <div className="relative w-64 h-64 overflow-hidden rounded-lg">
                  <Image src="/placeholder.svg?height=256&width=256" alt="About Me" fill className="object-cover" />
                </div>
              </div>
              <div className="md:w-2/3 space-y-4">
                <p className="text-lg">
                  I'm a BTech student in my third year, specializing in Computer Science. My journey in technology
                  started with a curiosity about how digital products are built, which led me to explore various
                  programming languages and frameworks.
                </p>
                <p className="text-lg">
                  Currently, I'm focused on full-stack development, with a particular interest in creating intuitive
                  user interfaces and robust backend systems. I believe in writing clean, maintainable code and building
                  applications that solve real-world problems.
                </p>
                <p className="text-lg font-medium">
                  My philosophy: "Building with empathy and precision, learning something new every day."
                </p>
                <div className="flex gap-2 pt-2">
                  <Badge>Problem Solver</Badge>
                  <Badge>Team Player</Badge>
                  <Badge>Continuous Learner</Badge>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Projects Section */}
        <section id="projects" className="container py-12 md:py-24">
          <h2 className="text-3xl md:text-4xl font-bold text-center mb-12">My Projects</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProjectCard
              title="E-Commerce Platform"
              description="A full-stack e-commerce application with user authentication, product catalog, and payment integration."
              techStack={["React", "Node.js", "MongoDB", "Stripe"]}
              githubLink="https://github.com/username/ecommerce"
              demoLink="https://demo-ecommerce.vercel.app"
              image="/placeholder.svg?height=200&width=300"
            />
            <ProjectCard
              title="Task Management App"
              description="A productivity application for managing tasks with features like drag-and-drop, categories, and reminders."
              techStack={["Vue.js", "Firebase", "Tailwind CSS"]}
              githubLink="https://github.com/username/task-manager"
              demoLink="https://task-manager-demo.netlify.app"
              image="/placeholder.svg?height=200&width=300"
            />
            <ProjectCard
              title="Weather Dashboard"
              description="A weather application that displays current conditions and forecasts using data from a weather API."
              techStack={["JavaScript", "OpenWeather API", "Chart.js"]}
              githubLink="https://github.com/username/weather-app"
              demoLink="https://weather-dashboard-demo.vercel.app"
              image="/placeholder.svg?height=200&width=300"
            />
            <ProjectCard
              title="Social Media Analytics"
              description="A dashboard for tracking and analyzing social media metrics across multiple platforms."
              techStack={["React", "D3.js", "Express", "PostgreSQL"]}
              githubLink="https://github.com/username/social-analytics"
              demoLink="https://social-analytics-demo.vercel.app"
              image="/placeholder.svg?height=200&width=300"
            />
            <ProjectCard
              title="Fitness Tracker"
              description="A mobile-responsive application for tracking workouts, nutrition, and fitness goals."
              techStack={["React Native", "Redux", "Node.js", "MongoDB"]}
              githubLink="https://github.com/username/fitness-tracker"
              demoLink="https://fitness-tracker-demo.vercel.app"
              image="/placeholder.svg?height=200&width=300"
            />
            <ProjectCard
              title="Portfolio Website"
              description="This portfolio website built with Next.js and Tailwind CSS to showcase my projects and skills."
              techStack={["Next.js", "Tailwind CSS", "React"]}
              githubLink="https://github.com/username/portfolio"
              demoLink="#"
              image="/placeholder.svg?height=200&width=300"
            />
          </div>
        </section>

        {/* Skills Section */}
        <section id="skills" className="w-full py-12 md:py-24 bg-muted/50">
          <div className="container">
            <h2 className="text-3xl md:text-4xl font-bold text-center mb-12">My Skills</h2>

            <div className="space-y-8">
              <div>
                <h3 className="text-xl font-semibold mb-4">Programming Languages</h3>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <SkillCard name="JavaScript" level={90} />
                  <SkillCard name="Python" level={85} />
                  <SkillCard name="Java" level={75} />
                  <SkillCard name="C++" level={70} />
                  <SkillCard name="TypeScript" level={80} />
                </div>
              </div>

              <div>
                <h3 className="text-xl font-semibold mb-4">Web Development</h3>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <SkillCard name="HTML" level={95} />
                  <SkillCard name="CSS" level={90} />
                  <SkillCard name="React" level={85} />
                  <SkillCard name="Node.js" level={80} />
                  <SkillCard name="Next.js" level={75} />
                  <SkillCard name="Tailwind" level={85} />
                  <SkillCard name="Express" level={75} />
                  <SkillCard name="Vue.js" level={65} />
                </div>
              </div>

              <div>
                <h3 className="text-xl font-semibold mb-4">Tools & Technologies</h3>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <SkillCard name="Git" level={90} />
                  <SkillCard name="Docker" level={70} />
                  <SkillCard name="VS Code" level={95} />
                  <SkillCard name="Figma" level={75} />
                  <SkillCard name="AWS" level={65} />
                </div>
              </div>

              <div>
                <h3 className="text-xl font-semibold mb-4">Databases</h3>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <SkillCard name="MongoDB" level={85} />
                  <SkillCard name="MySQL" level={80} />
                  <SkillCard name="PostgreSQL" level={75} />
                  <SkillCard name="Firebase" level={80} />
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Contact Section */}
        <section id="contact" className="container py-12 md:py-24">
          <h2 className="text-3xl md:text-4xl font-bold text-center mb-12">Get In Touch</h2>
          <div className="grid md:grid-cols-2 gap-8">
            <Card>
              <CardContent className="p-6">
                <form className="space-y-4">
                  <div className="space-y-2">
                    <label
                      htmlFor="name"
                      className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                      Name
                    </label>
                    <input
                      id="name"
                      className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                      placeholder="Your name"
                    />
                  </div>
                  <div className="space-y-2">
                    <label
                      htmlFor="email"
                      className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                      Email
                    </label>
                    <input
                      id="email"
                      type="email"
                      className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                      placeholder="Your email"
                    />
                  </div>
                  <div className="space-y-2">
                    <label
                      htmlFor="message"
                      className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                      Message
                    </label>
                    <textarea
                      id="message"
                      className="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                      placeholder="Your message"
                    />
                  </div>
                  <Button className="w-full">Send Message</Button>
                </form>
              </CardContent>
            </Card>

            <div className="flex flex-col justify-center space-y-6">
              <div className="flex items-center gap-4">
                <div className="bg-primary/10 p-3 rounded-full">
                  <Mail className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-lg font-medium">Email</h3>
                  <a href="mailto:contact@johndoe.com" className="text-muted-foreground hover:text-primary">
                    contact@johndoe.com
                  </a>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="bg-primary/10 p-3 rounded-full">
                  <Linkedin className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-lg font-medium">LinkedIn</h3>
                  <a
                    href="https://linkedin.com/in/johndoe"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-muted-foreground hover:text-primary"
                  >
                    linkedin.com/in/johndoe
                  </a>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="bg-primary/10 p-3 rounded-full">
                  <Github className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-lg font-medium">GitHub</h3>
                  <a
                    href="https://github.com/johndoe"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-muted-foreground hover:text-primary"
                  >
                    github.com/johndoe
                  </a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="w-full border-t py-6 md:py-0">
        <div className="container flex flex-col md:flex-row items-center justify-between gap-4 md:h-16">
          <p className="text-sm text-muted-foreground">© John Doe 2025. All rights reserved.</p>
          <nav className="flex gap-4 md:gap-6">
            <Link
              href="#home"
              className="text-xs md:text-sm text-muted-foreground hover:text-primary transition-colors"
            >
              Home
            </Link>
            <Link
              href="#about"
              className="text-xs md:text-sm text-muted-foreground hover:text-primary transition-colors"
            >
              About
            </Link>
            <Link
              href="#projects"
              className="text-xs md:text-sm text-muted-foreground hover:text-primary transition-colors"
            >
              Projects
            </Link>
            <Link
              href="#skills"
              className="text-xs md:text-sm text-muted-foreground hover:text-primary transition-colors"
            >
              Skills
            </Link>
            <Link
              href="#contact"
              className="text-xs md:text-sm text-muted-foreground hover:text-primary transition-colors"
            >
              Contact
            </Link>
          </nav>
          <div className="flex items-center gap-3">
            <a href="https://github.com/johndoe" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
              <Github className="h-4 w-4 text-muted-foreground hover:text-primary transition-colors" />
            </a>
            <a href="https://linkedin.com/in/johndoe" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
              <Linkedin className="h-4 w-4 text-muted-foreground hover:text-primary transition-colors" />
            </a>
            <a href="mailto:contact@johndoe.com" aria-label="Email">
              <Mail className="h-4 w-4 text-muted-foreground hover:text-primary transition-colors" />
            </a>
          </div>
        </div>
      </footer>
    </div>
  )
}
