import Link from "next/link"
import { env } from "~/env"
import { NavbarMenuItem } from "./NavbarMenuItem"
import { CollapseButton } from "../action/CollapseButton"
import { LoginButton } from "../action/LoginButton"
import { getServerAuthSession } from "~/lib/auth"

export async function Navbar() {
  const session = await getServerAuthSession()

  return (
    <nav className="flex w-full flex-wrap items-center p-4 md:gap-8 dark:bg-gray-800">
      <Link href={env.NEXTAUTH_URL} className="flex items-center space-x-3">
        <>
          {/* 
            <img
              src="/logo.svg"
              className="h-8"
              alt="Logo" />
            */}
          <span className="self-center whitespace-nowrap text-2xl font-semibold dark:text-white">
            {env.TITLE}
          </span>
        </>
      </Link>

      <div className="grow">
        {session && (
          <span className="hidden md:flex">
            Willkommen {session?.user.vorname} {session?.user.nachname}
          </span>
        )}
      </div>
      <LoginButton className={"mr-4 md:order-2 md:mr-0"} session={session} />

      <CollapseButton
        className="w-full items-center gap-8 md:order-1 md:flex md:w-fit"
        srLabel="Navbar Menu"
      >
        <ul className="mt-4 flex flex-col rounded-lg border border-gray-100 bg-gray-50 p-4 font-medium md:mt-0 md:flex-row md:space-x-8 md:border-0 md:bg-white md:p-0 dark:border-gray-600 dark:bg-gray-700 md:dark:bg-gray-800">
          <NavbarMenuItem href={"/"}>Home</NavbarMenuItem>
          {session && session.user.role === "students" && (
            <NavbarMenuItem href={"/selection"}>Wahl</NavbarMenuItem>
          )}
          <NavbarMenuItem href={"/projects"}>Projekte</NavbarMenuItem>
          <NavbarMenuItem href={"/impressum"}>Impressum</NavbarMenuItem>
        </ul>
      </CollapseButton>
    </nav>
  )
}
