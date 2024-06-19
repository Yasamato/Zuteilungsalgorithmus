"use client"

import Link from "next/link"
import { usePathname } from "next/navigation"
import { useEffect, useMemo, useState } from "react"

export function NavbarMenuItem({
  className,
  ...props
}: React.ComponentProps<typeof Link>) {
  const path = usePathname()
  const [active, setActive] = useState(path === props.href)

  useEffect(() => {
    setActive(path === props.href)
  }, [path, props.href])

  const linkClassName = useMemo(() => {
    let tmp = "block rounded px-3 py-2 md:p-0 dark:text-white "

    if (active) {
      tmp +=
        "text-white bg-blue-700 md:bg-transparent md:p-0 md:text-blue-700 md:dark:text-blue-500"
    } else {
      tmp +=
        "text-gray-900 hover:bg-gray-100 md:border-0 md:p-0 md:hover:bg-transparent md:hover:text-blue-700 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent md:dark:hover:text-blue-500"
    }

    if (className) {
      tmp += " " + className
    }

    return tmp
  }, [active, className])

  return (
    <li>
      <Link {...props} className={linkClassName} />
    </li>
  )
}
