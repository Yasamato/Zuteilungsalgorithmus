import {
  boolean,
  integer,
  pgTableCreator,
  serial,
  text,
} from "drizzle-orm/pg-core"

/**
 * This is an example of how to use the multi-project schema feature of Drizzle ORM. Use the same
 * database instance for multiple projects.
 *
 * @see https://orm.drizzle.team/docs/goodies#multi-project-schema
 */
export const createTable = pgTableCreator(
  (name) => `zuteilungsalgorithmus_${name}`,
)

export const days = createTable("day", {
  id: serial("id").primaryKey(),
  day: text("day"),
  // starting time
  start: text("start"),
  // ending time
  stop: text("stop"),
})

export interface Program {
  dayId: number
  morning: string
  mensa: boolean
  afternoon: string
}

export const classes = createTable("class", {
  class: text("class").primaryKey(),
  students: integer("students").notNull(),
})

export const projects = createTable("project", {
  id: serial("id").primaryKey(),
  name: text("name").notNull().unique(),
  description: text("description").notNull(),
  supervisor: text("supervisor").notNull(),
  minClass: integer("minClass").notNull(),
  maxClass: integer("maxClass").notNull(),
  minSeats: integer("minSeats").notNull(),
  maxSeats: integer("maxSeats").notNull(),
  prerequisites: text("prerequisites"),
  other: text("other"),
  room: text("room"),
  material: text("material"),
  // JSON.stringify array of Program
  program: text("program"),
})

export const selections = createTable("selection", {
  id: serial("id").primaryKey(),
  vorname: text("vorname").notNull(),
  nachname: text("nachname").notNull(),
  stufe: text("stufe").notNull(),
  class: integer("class").notNull(),
  // JSON.stringify array of project ids
  selection: text("selection").default("[]"),
  // Forcefully allocated to a project, disables selection for user
  compulsory: boolean("compulsory").default(false),
  allocation: integer("allocation"),
})
