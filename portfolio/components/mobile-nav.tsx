"use client"

import { useState } from "react"
import Link from "next/link"
import { Menu } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"

export default function MobileNav() {
  const [open, setOpen] = useState(false)

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button variant="ghost" size="icon" className="md:hidden">
          <Menu className="h-5 w-5" />
          <span className="sr-only">Toggle menu</span>
        </Button>
      </SheetTrigger>
      <SheetContent side="right" className="w-[300px] sm:w-[400px]">
        <div className="flex flex-col gap-4 mt-8">
          <Link
            href="#home"
            className="text-lg font-medium py-2 hover:text-primary transition-colors"
            onClick={() => setOpen(false)}
          >
            Home
          </Link>
          <Link
            href="#about"
            className="text-lg font-medium py-2 hover:text-primary transition-colors"
            onClick={() => setOpen(false)}
          >
            About Me
          </Link>
          <Link
            href="#projects"
            className="text-lg font-medium py-2 hover:text-primary transition-colors"
            onClick={() => setOpen(false)}
          >
            Projects
          </Link>
          <Link
            href="#skills"
            className="text-lg font-medium py-2 hover:text-primary transition-colors"
            onClick={() => setOpen(false)}
          >
            Skills
          </Link>
          <Link
            href="#contact"
            className="text-lg font-medium py-2 hover:text-primary transition-colors"
            onClick={() => setOpen(false)}
          >
            Contact
          </Link>
        </div>
      </SheetContent>
    </Sheet>
  )
}
