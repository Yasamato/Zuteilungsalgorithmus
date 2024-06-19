import { db } from "~/lib/db"

export default async function ProjectsPage() {
  const projects = await db.query.projects.findMany()
  return (
    <ul>
      {projects.map((project) => (
        <li key={project.id}>{project.name}</li>
      ))}
    </ul>
  )
}
