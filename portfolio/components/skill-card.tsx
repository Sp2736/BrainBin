import { Card, CardContent } from "@/components/ui/card"
import { Progress } from "@/components/ui/progress"

interface SkillCardProps {
  name: string
  level: number
}

export default function SkillCard({ name, level }: SkillCardProps) {
  return (
    <Card className="overflow-hidden">
      <CardContent className="p-4">
        <div className="text-center mb-2">{name}</div>
        <Progress value={level} className="h-2" />
      </CardContent>
    </Card>
  )
}
